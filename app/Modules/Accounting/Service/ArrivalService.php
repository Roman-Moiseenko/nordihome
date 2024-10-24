<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\ArrivalHasCompleted;
use App\Events\MovementHasCreated;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Service\OrderReserveService;
use App\Modules\Product\Entity\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;

class ArrivalService
{
    private StorageService $storages;
    private DistributorService $distributors;
    private OrderReserveService $reserveService;
    private MovementService $movementService;

    public function __construct(
        StorageService $storages,
        DistributorService $distributors,
        OrderReserveService $reserveService,
        MovementService $movementService
    )
    {
        $this->storages = $storages;
        $this->distributors = $distributors;
        $this->reserveService = $reserveService;
        $this->movementService = $movementService;
    }

    public function create(int $distributor_id, bool $is_manager = true): ArrivalDocument
    {
        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();
        /** @var Distributor $distributor */
        $distributor = Distributor::find($distributor_id);
        $storage = Storage::where('default', true)->first();
        return ArrivalDocument::register(
            '',
            $distributor->id,
            $storage->id,
            $distributor->currency,
            '',
            $is_manager ? $manager->id : null
        );
    }

    public function update(Request $request, ArrivalDocument $arrival): ArrivalDocument
    {
        DB::transaction(function () use ($request, $arrival) {
            if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
            $arrival->number = $request->string('number')->trim()->value();
            $arrival->distributor_id = $request->integer('distributor');
            $arrival->storage_id = $request->integer('storage');
            $arrival->save();
            /** @var Distributor $distributor */
            $distributor = Distributor::find($arrival->distributor_id);
            $currency = $distributor->currency;
            if ($currency->id != $arrival->currency_id) {
                $arrival->currency_id = $currency->id;
                $arrival->setExchange($currency->getExchange());
            } elseif ($currency->getExchange() != $arrival->exchange_fix) {
                $arrival->setExchange($currency->getExchange());
            }
        });
        return $arrival;
    }

    public function destroy(ArrivalDocument $arrival)
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $arrival->delete();
    }

    /**
     * @param ArrivalDocument $arrival
     * @param int $product_id
     * @param int $quantity
     * @return ArrivalProduct|null
     */
    public function add(ArrivalDocument $arrival, int $product_id, int $quantity):? ArrivalProduct
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        /** @var Product $product */
        $product = Product::find($product_id);

        if ($arrival->isProduct($product_id)) {
            flash('Товар ' . $product->name . ' уже добавлен в документ', 'warning');
            return null;
        }

        $distributor_cost = is_null($arrival->distributor) ? 0 : $arrival->distributor->getCostItem($product->id); //Ищем у поставщика товар, если есть, берем закупочную цену
        $product_sell = $product->getLastPrice();

        //Добавляем в документ
        $item = ArrivalProduct::new(
            $product->id,
            $quantity,
            $distributor_cost,
            $arrival->exchange_fix * $distributor_cost,
            $product_sell
        );
        //$product->refresh();
        $arrival->arrivalProducts()->save($item);
        //$arrival->refresh();

        return $item;
    }

    public function add_products(ArrivalDocument $arrival, string $textarea): void
    {
        $list = explode("\r\n", $textarea);
        foreach ($list as $item) {
            $product = Product::whereCode($item)->first();
            if (!is_null($product)) {
                $this->add($arrival, $product->id, 1);
            } else {
                flash('Товар с артикулом ' . $item . ' не найден', 'danger');
            }
        }
    }

    //Для AJAX
    public function set(Request $request, ArrivalProduct $item): float|int
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        //Меняем данные
        $item->quantity = $request->integer('quantity');
        $item->cost_currency = $request->float('cost');
        $item->cost_ru = ceil($item->document->exchange_fix * $item->cost_currency * 100) / 100;
        $item->price_sell = $request->integer('price');
        $item->save();
        return $item->cost_ru;
    }

    #[ArrayShape(['cost_currency' => "float|int", 'quantity' => "int", 'cost_ru' => "float|int"])]
    public function getInfoData(ArrivalDocument $arrival): array
    {
        $result = [
            'cost_currency' => 0,
            'quantity' => 0,
            'cost_ru' => 0,
        ];

        foreach ($arrival->arrivalProducts as $item) {
            $result['quantity'] += $item->quantity;
            $result['cost_currency'] += $item->quantity * $item->cost_currency;
        }
        $result['cost_ru'] = ceil($result['cost_currency'] * $arrival->exchange_fix * 100) / 100;

        return $result;
    }

    /**
     * Проведение документа, с установкой новой цены продажи и закупа у поставщика
     * @param ArrivalDocument $arrival
     * @return void
     */
    public function completed(ArrivalDocument $arrival)
    {
        DB::transaction(function () use ($arrival) {
            $this->storages->arrival($arrival->storage, $arrival->arrivalProducts()->getModels());
            //Проходим все товары и добавляем Поставщику с новой ценой, если она изменилась или товара нет
            foreach ($arrival->arrivalProducts as $item) {
                $this->distributors->arrival($arrival->distributor, $item->product_id, $item->cost_currency);
            }
            $arrival->completed();

            //У поступления есть заказ поставщику
            if (!is_null($arrival->supply)) {
                //Создаем перемещения под все заказы со статусом на отбытие
                $orders_movement = [];
                $storages_movement = [];
                $orders = [];
                foreach ($arrival->supply->stacks as $stack) {
                    //Поступления, для которых есть стек из заказа, ставим в резерв
                    if (!is_null($stack->orderItem)) {
                        $this->reserveService->toReserveStorage($stack->orderItem, $arrival->storage, $stack->quantity);
                        $stack->orderItem->preorder = false;
                        $stack->orderItem->save();
                        //сохраняем список $orders для оповещения
                        $orders[$stack->orderItem->order_id] = $stack->orderItem->order;
                    }

                    if ($stack->storage_id != $arrival->storage_id) { //Хранилище отличается, требуется Перемещение

                        if (!is_null($stack->orderItem)) { //Из заказа
                            $orders_movement[$stack->orderItem->order->id][] = $stack;
                        } else { //Перемещение от менеджера
                            $storages_movement[$stack->storage_id][] = $stack;
                        }
                    }

                }

                //Создаем перемещения для заказов
                /** @var SupplyStack $stack */
                foreach ($orders_movement as $order_id => $stacks) {
                    /** @var Order $order */
                    $order = Order::find($order_id);
                    $movement = $this->movementService->create(
                        $arrival->storage_id,
                        $stacks[0]->storage_id //По первому элементу определяем хранилище
                    );

                    $order->movements()->attach($movement->id);

                    foreach ($stacks as $stack) {
                        $movement->addProduct($stack->product, $stack->quantity, $stack->orderItem->id);
                    }
                    $movement->refresh();
                    $this->movementService->activate($movement);
                    event(new MovementHasCreated($movement));
                }
                //Создаем перемещения от менеджеров
                /** @var SupplyStack $stack */
                foreach ($storages_movement as $storageIn_id => $stacks) {
                    $movement = $this->movementService->create($arrival->storage_id, $storageIn_id);
                    foreach ($stacks as $stack) {
                        $movement->addProduct($stack->product, $stack->quantity);
                    }
                    $movement->refresh();
                    $this->movementService->activate($movement);
                    event(new MovementHasCreated($movement));
                }
            }

            event(new ArrivalHasCompleted($arrival));
        });
    }

}
