<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\ArrivalHasCompleted;
use App\Events\MovementHasCreated;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Order\Service\OrderReserveService;

use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function create(array $request): ArrivalDocument
    {
        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();
        /** @var Distributor $distributor */
        $distributor = Distributor::find((int)$request['distributor']);
        return ArrivalDocument::register(
            $request['number'] ?? '',
            $distributor->id,
            (int)$request['storage'],
            $distributor->currency,
            $request['comment'] ?? '',
            $manager->id
        );
    }

    public function update(Request $request, ArrivalDocument $arrival): ArrivalDocument
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $arrival->number = $request['number'] ?? '';
        $arrival->distributor_id = (int)$request['distributor'];
        $arrival->storage_id = (int)$request['storage'];
        $arrival->save();
        /** @var Distributor $distributor */
        $distributor = Distributor::find((int)$request['distributor']);
        $currency = $distributor->currency;
        if ($currency->id != $arrival->currency_id) {
            $arrival->currency_id = $currency->id;
            $arrival->setExchange($currency->exchange);
        } elseif ($currency->exchange != $arrival->exchange_fix) {
            $arrival->setExchange($currency->exchange);
        }
        return $arrival;
    }

    public function destroy(ArrivalDocument $arrival)
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $arrival->delete();
    }

    /**
     * @param ArrivalDocument $arrival
     * @param array{product_id: int, quantity: int} $request
     * @return ArrivalProduct
     */
    public function add(ArrivalDocument $arrival, array $request): ArrivalProduct
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($request['product_id']);
        $distributor_cost = $arrival->distributor->getCostItem($product->id); //Ищем у поставщика товар, если есть, берем закупочную цену
        $product_sell = $product->getLastPrice();

        //Добавляем в документ
        $item = ArrivalProduct::new(
            $product->id,
            (int)$request['quantity'],
            $distributor_cost,
            $arrival->exchange_fix * $distributor_cost,
            $product_sell
        );
        $product->refresh();

        $arrival->arrivalProducts()->save($item);
        $arrival->refresh();

        return $item;
    }

    //Для AJAX
    public function set(Request $request, ArrivalProduct $item): float|int
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        //Меняем данные
        $item->quantity = (int)$request['quantity'];
        $item->cost_currency = (float)$request['cost'];
        $item->cost_ru = ceil($item->document->exchange_fix * $item->cost_currency * 100) / 100;
        $item->price_sell = (int)$request['price'];
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
        $this->storages->arrival($arrival->storage, $arrival->arrivalProducts()->getModels());
        //Проходим все товары и добавляем Поставщику с новой ценой, если она изменилась или товара нет
        foreach ($arrival->arrivalProducts as $item) {
            $this->distributors->arrival($arrival->distributor, $item->product_id, $item->cost_currency);
            $item->product->setPrice($item->price_sell);
        }

        $arrival->completed();


        //У поступления есть заказ поставщику
        if (!is_null($arrival->supply)) {
            //Создаем перемещения под все заказы со статусом на отбытие
            $orders_movement = [];
            $storages_movement = [];
            foreach ($arrival->supply->stacks as $stack) {
                //Поступления, для которых есть стек из заказа, ставим в резерв
                if (!is_null($stack->orderItem)) {
                    $this->reserveService->toReserveStorage($stack->orderItem, $arrival->storage, $stack->quantity);
                    $stack->orderItem->preorder = false;
                    $stack->orderItem->save();
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
                $movement = $this->movementService->create([
                    'number' => 'Заказ ' . $order->htmlNum(),
                    'storage_out' => $arrival->storage_id,
                    'storage_in' => $stacks[0]->storage_id, //По первому элементу определяем хранилище
                ]);

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
                $movement = $this->movementService->create([
                    'number' => 'Заказ от менеджеров',
                    'storage_out' => $arrival->storage_id,
                    'storage_in' => $storageIn_id,
                ]);
                foreach ($stacks as $stack) {
                    $movement->addProduct($stack->product, $stack->quantity);
                }
                $movement->refresh();
                $this->movementService->activate($movement);
                event(new MovementHasCreated($movement));
            }

            //Проходим по стеку к заказу поставщика
            /*
            foreach ($arrival->supply->stacks as $stack) {
                //Если у стека есть элемент Заказа Клиента и совпадает Хранилище
                if (!empty($stack->orderItem)) {
                    $reserve = $this->reserveService->toReserve(
                        $stack->orderItem->product,
                        $stack->orderItem->quantity,
                        'order',
                        24 * 60 * 3,
                        $stack->orderItem->order->user_id);

                    $stack->orderItem->reserve_id = $reserve->id;
                    $stack->orderItem->save();
                    if ($stack->storage_id == $arrival->storage_id) {
                        $reserve->storage_id = $stack->storage_id;
                        $reserve->save();
                    }
                }

            }
            */
        }

        event(new ArrivalHasCompleted($arrival));
    }

}
