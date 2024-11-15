<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\ArrivalHasCompleted;
use App\Events\MovementHasCreated;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\RefundProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SupplyProduct;
use App\Modules\Accounting\Entity\SupplyStack;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Service\OrderReserveService;
use App\Modules\Product\Entity\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class ArrivalService
{
    private StorageService $storages;
    private OrderReserveService $reserveService;
    private MovementService $movementService;
    private RefundService $refundService;
    private ArrivalExpenseService $expenseService;

    public function __construct(
        StorageService      $storages,
        OrderReserveService $reserveService,
        MovementService     $movementService,
        RefundService       $refundService,
        ArrivalExpenseService $expenseService,
    )
    {
        $this->storages = $storages;
        $this->reserveService = $reserveService;
        $this->movementService = $movementService;
        $this->refundService = $refundService;
        $this->expenseService = $expenseService;
    }

    public function create(int $distributor_id, bool $is_manager = true): ArrivalDocument
    {
        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();
        /** @var Distributor $distributor */
        $distributor = Distributor::find($distributor_id);
        $storage = Storage::where('default', true)->first();
        return ArrivalDocument::register(
            $distributor->id,
            $storage->id,
            $distributor->currency,
            $is_manager ? $manager->id : null
        );
    }

    #[Deprecated]
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

    public function destroy(ArrivalDocument $arrival): void
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $arrival->expense()->delete();
        $arrival->delete();
    }

    /**
     * Добавляем товар в поступление. Учет основания По заказу или Свободное добавление
     */
    public function addProduct(ArrivalDocument $arrival, int $product_id, int $quantity): ?ArrivalProduct
    {
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $distributor_cost = 0;
        /** @var Product $product */
        $product = Product::find($product_id);
        if ($arrival->isSupply()) {
            /** @var SupplyProduct $supplyProduct */
            $supplyProduct = $arrival->supply->getProduct($product->id);
            if (is_null($supplyProduct)) throw new \DomainException('Товар ' . $product->name . ' отсутствует в связанном документе.');
            $quantity = min($quantity, $supplyProduct->getQuantityUnallocated());
            if ($quantity <= 0) throw new \DomainException('Недостаточное кол-во товара ' . $product->name . ' в связанном документе.');

            $distributor_cost = $supplyProduct->cost_currency;
        }
        //Если товар уже есть в Поступлении
        if ($arrival->isProduct($product_id)) {
            $arrivalProduct = $arrival->getProduct($product_id);
            $arrivalProduct->addQuantity($quantity);
            return null;
        }
        if ($distributor_cost == 0 && !is_null($arrival->distributor))
            $distributor_cost = $arrival->distributor->getCostItem($product->id); //Ищем у поставщика товар и берем закупочную цену
        //Добавляем в документ если его нет
        $item = ArrivalProduct::new(
            $product->id,
            $quantity,
            $distributor_cost,
        );
        $arrival->products()->save($item);

        return $item;
    }

    public function addProducts(ArrivalDocument $arrival, mixed $products): void
    {
        $errors = [];
        foreach ($products as $product) {
            $product_id = Product::whereCode($product['code'])->first()->id;
            if (!is_null($product)) {
                $this->addProduct($arrival, $product_id, (int)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }

    public function setProduct(ArrivalProduct $arrivalProduct, int $quantity, float $cost): void
    {
        $arrival = $arrivalProduct->document;
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        if ($arrival->isSupply()) { //Есть связанный документ
            //Нельзя менять валюту
            if ($arrivalProduct->cost_currency != $cost) throw new \DomainException('Стоимость установлена в связанном документа!');
            $unallocated = $arrivalProduct->getSupplyProduct()->getQuantityUnallocated();//Доступное кол-во
            $delta = min($quantity - $arrivalProduct->quantity, $unallocated);
            //dd([$unallocated, $delta, $arrivalProduct->quantity]);
            if ($delta == 0) throw new \DomainException('Недостаточное кол-во товара ' . $arrivalProduct->product->name . ' в связанном документе.');
            $arrivalProduct->addQuantity($delta);
            $arrivalProduct->refresh();
           // dd([$unallocated, $delta, $arrivalProduct->quantity]);
            return;
        };
        //Меняем данные
        $arrivalProduct->setQuantity($quantity);
        $arrivalProduct->setCost($cost);
        $arrivalProduct->save();
    }

    /**
     * Проведение документа, с установкой новой цены продажи и закупа у поставщика
     */
    public function completed(ArrivalDocument $arrival): void
    {
        DB::transaction(function () use ($arrival) {
            if (!is_null($arrival->expense)) $this->expenseService->completed($arrival->expense); //Провести доп.расходы
            $this->storages->arrival($arrival->storage, $arrival->products); //Поступление на склад
            //Проходим все товары и добавляем Поставщику с новой ценой, если она изменилась или товара нет
            foreach ($arrival->products as $item) {
                $arrival->distributor->addProduct($item->product, $item->cost_currency);
            }
            $arrival->completed();
            //TODO Проверить и ?Переработать механизм
            //У поступления есть заказ поставщику
            if (!is_null($arrival->supply)) {
                //Создаем перемещения под все заказы со статусом на отбытие
                $orders_movement = [];
                $storages_movement = [];
                $orders = []; //TODO Не используется ????!!!!!
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
                //TODO В перемещение привязать основание - Документ Arrival
                /** @var SupplyStack $stack */
                foreach ($orders_movement as $order_id => $stacks) {
                    /** @var Order $order */
                    $order = Order::find($order_id);
                    $movement = $this->movementService->create(
                        $arrival->storage_id,
                        $stacks[0]->storage_id, //По первому элементу определяем хранилище
                        $arrival->id,
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

    public function work(ArrivalDocument $arrival): void
    {
        DB::transaction(function () use ($arrival) {
            $arrival->work();
            $this->storages->departure($arrival->storage, $arrival->products);
            foreach ($arrival->products as $item) {//Проверка на отрицательное кол-во
                if ($arrival->storage->getQuantity($item->product) < 0)
                    throw new \DomainException('Нельзя отменить проведение. Остаток ' . $item->product->name . ' < 0');
            }
            if (!is_null($arrival->supply)) {
                foreach ($arrival->supply->stacks as $stack) {
                    //Поступления, для которых есть стек из заказа, удаляем из резерва
                    if (!is_null($stack->orderItem)) {
                        $this->reserveService->deleteReserveStorage($stack->orderItem, $arrival->storage, $stack->quantity);
                        $stack->orderItem->preorder = true;
                        $stack->orderItem->save();
                    }
                }
                //TODO Удалить перемещения
                /* Подготовленный код:
                $movements = MovementDocument::where('arrival_id', $arrival->id)->get();
                foreach ($movements as $movement) {
                    $movement->delete();
                }
    */
            }
            $arrival->expense->work();
        });
    }

    public function setInfo(ArrivalDocument $arrival, Request $request): void
    {
        $arrival->baseSave($request->input('document'));
        $arrival->exchange_fix = $request->input('exchange_fix');
        $arrival->storage_id = $request->integer('storage_id');
        $arrival->operation = $request->input('operation') ?? ArrivalDocument::OPERATION_SUPPLY;
        if ($request->input('gtd')) $arrival->gtd = $request->string('gtd')->value();
        $arrival->save();
    }

    //На основании
    public function expense(ArrivalDocument $arrival)
    {
        if (!is_null($arrival->expense)) return $arrival->expense;
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Вносить изменения нельзя');

        return $this->expenseService->create($arrival);
    }

    public function movement(ArrivalDocument $arrival)
    {
        //TODO В разработке
        throw new \DomainException('В разработке');
    }

    public function invoice(ArrivalDocument $arrival)
    {
        //TODO В разработке
        throw new \DomainException('В разработке');
    }

    public function refund(ArrivalDocument $arrival)
    {

        $refund = $this->refundService->create($arrival->id);
        //$refund->arrival_id = $arrival->id;
        //$refund->storage_id = $arrival->storage_id;
        //$refund->save();
        //Переносим весь не выданный товар в возврат
        foreach ($arrival->products as $product) {
            $quantity = $product->getQuantityUnallocated();
            if ($quantity > 0) {
                $item = RefundProduct::new(
                    $product->product_id,
                    $quantity,//Высчитываем свободное кол-во
                    $product->cost_currency,
                );
                $refund->products()->save($item);
            }
        }
        $refund->refresh();
        if ($refund->products()->count() == 0) {
            $refund->delete();
            throw new \DomainException('Все позиции возвращены. Возврат не доступен');
        }
        return $refund;
    }


}
