<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\ArrivalHasCompleted;
use App\Events\MovementHasCreated;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalExpenseDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\PricingProduct;
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


class ArrivalService
{
    private StorageService $storages;
    private OrderReserveService $reserveService;
    private MovementService $movementService;
    private RefundService $refundService;
    private ArrivalExpenseService $expenseService;
    private PricingService $pricingService;

    public function __construct(
        StorageService      $storages,
        OrderReserveService $reserveService,
        MovementService     $movementService,
        RefundService       $refundService,
        ArrivalExpenseService $expenseService,
        PricingService        $pricingService,
    )
    {
        $this->storages = $storages;
        $this->reserveService = $reserveService;
        $this->movementService = $movementService;
        $this->refundService = $refundService;
        $this->expenseService = $expenseService;
        $this->pricingService = $pricingService;
    }

    public function create(int $distributor_id, bool $is_manager = true): ArrivalDocument
    {
        /** @var Admin $manager */
        $staff = Auth::guard('admin')->user();
        /** @var Distributor $distributor */
        $distributor = Distributor::find($distributor_id);
        $storage = Storage::where('default', true)->first();
        return ArrivalDocument::register(
            $distributor->id,
            $storage->id,
            $distributor->currency,
            $is_manager ? $staff->id : null
        );
    }

    public function create_storage(int $storage_id): ArrivalDocument
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $currency = Currency::where('name', 'Рубль')->first();
        return ArrivalDocument::register(
            null,
            $storage_id,
            $currency,
            $staff->id
        );
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
    public function addProduct(ArrivalDocument $arrival, int $product_id, float $quantity): ?ArrivalProduct
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

    public function addProducts(ArrivalDocument $arrival, array $products): void
    {
        $errors = [];
        foreach ($products as $product) {
            $_product = Product::whereCode($product['code'])->first();
            if (!is_null($_product)) {
                $this->addProduct($arrival, $_product->id, (float)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }

    public function setProduct(ArrivalProduct $arrivalProduct, float $quantity, float $cost): void
    {

        $arrival = $arrivalProduct->document;
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        if ($arrival->isSupply()) { //Есть связанный документ
            //Нельзя менять валюту
            if ($arrivalProduct->cost_currency != $cost) throw new \DomainException('Стоимость установлена в связанном документа!');
            $unallocated = $arrivalProduct->getSupplyProduct()->getQuantityUnallocated();//Доступное кол-во
            $delta = min($quantity - $arrivalProduct->quantity, $unallocated);
            if ($delta == 0) throw new \DomainException('Недостаточное кол-во товара ' . $arrivalProduct->product->name . ' в связанном документе.');
            $arrivalProduct->addQuantity($delta);
            $arrivalProduct->refresh();
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
                $arrival->distributor->addProduct($item->product, (float)$item->cost_currency);
                if ($item->quantity == 0) throw new \DomainException('Некоторые позиции имеют нулевое кол-во');
                if ($item->cost_currency == 0) throw new \DomainException('Некоторые позиции имеют нулевую цену');
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
                        $this->reserveService->toReserveStorage($stack->orderItem, $arrival->storage, (float)$stack->quantity);
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
                        $movement->addProduct($stack->product_id, $stack->quantity, $stack->orderItem->id);
                    }
                    $movement->refresh();
                    $this->movementService->completed($movement);
                    event(new MovementHasCreated($movement));
                }
                //Создаем перемещения от менеджеров
                /** @var SupplyStack $stack */
                foreach ($storages_movement as $storageIn_id => $stacks) {
                    $movement = $this->movementService->create($arrival->storage_id, $storageIn_id);
                    foreach ($stacks as $stack) {
                        $movement->addProduct($stack->product_id, $stack->quantity);
                    }
                    $movement->refresh();
                    $this->movementService->completed($movement);
                    event(new MovementHasCreated($movement));
                }
            }
            event(new ArrivalHasCompleted($arrival));
        });
    }

    public function work(ArrivalDocument $arrival): void
    {
        DB::transaction(function () use ($arrival) {
            if (!is_null($arrival->pricing)) {
                if ($arrival->pricing->isCompleted()) {
                    throw new \DomainException('Проведен связанный документ Установка цен.');
                } else {
                    $arrival->pricing->delete();
                }
            }

            $arrival->work();
            $this->storages->departure($arrival->storage, $arrival->products);
            foreach ($arrival->products as $item) {//Проверка на отрицательное кол-во
                if ($arrival->storage->getQuantity($item->product_id) < 0)
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

                foreach ($arrival->movements as $movement) {
                    if ($movement->isCompleted())
                        throw new \DomainException('Проведен связанный документ Перемещение.');
                    $movement->delete();
                }
            }
            if (!is_null($arrival->expense)) $arrival->expense->work();
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
    public function expense(ArrivalDocument $arrival): ArrivalExpenseDocument
    {
        //if (!is_null($arrival->expense)) return $arrival->expense;
        if ($arrival->isCompleted()) throw new \DomainException('Документ проведен. Вносить изменения нельзя');

        return $this->expenseService->create($arrival);
    }

    public function movement(ArrivalDocument $arrival)
    {
        DB::transaction(function () use($arrival, &$movement) {
            $storageIn = Storage::where('id', '<>', $arrival->storage_id)->first();
            if (is_null($storageIn)) throw new \DomainException('Отсутствуют склады для перемещения');
            $movement = $this->movementService->create($arrival->storage_id, $storageIn->id, $arrival->id);
            foreach ($arrival->products as $product) {
                //Переместить можно кроме возвращенного товара за минусом уже перемещенного
                $quantity = $product->getQuantityUnallocated() - $product->getQuantityMoved();
                $movement->addProduct($product->product_id, $quantity);
            }
        });
        return $movement;
    }

    public function pricing(ArrivalDocument $arrival)
    {
        DB::transaction(function () use($arrival, &$pricing) {
            if (!$arrival->isCompleted()) throw new \DomainException('Документ не проведен. Создать документ цен нельзя');

            if (!is_null($arrival->pricing)) return $arrival->pricing;
            $coeff = 1;
            if (!is_null($arrival->expense)) $coeff += $arrival->expense->getAmount() / ($arrival->getAmount() * $arrival->exchange_fix);
            $pricing = $this->pricingService->create($arrival->id);
            foreach ($arrival->products as $product) {
                $cost_ru = $product->cost_currency * $arrival->exchange_fix;
                $item = PricingProduct::new(
                    product_id: $product->product_id,
                    price_cost: (int)ceil($cost_ru * $coeff),
                    price_min:  (int)ceil($cost_ru),
                );
                $pricing->products()->save($item);
            }
        });
        return $pricing;
    }

    public function refund(ArrivalDocument $arrival)
    {
        DB::transaction(function () use($arrival, &$refund) {
            $refund = $this->refundService->create($arrival->id);
            //Переносим весь не выданный товар в возврат
            foreach ($arrival->products as $product) {
                $quantity = $product->getQuantityUnallocated();
                if ($quantity > 0) {
                    $item = RefundProduct::new(
                        $product->product_id,
                        $quantity,//Высчитываем свободное кол-во
                        (float)$product->cost_currency,
                    );
                    $refund->products()->save($item);
                }
            }
        });

        $refund->refresh();
        if ($refund->products()->count() == 0) {
            $refund->delete();
            throw new \DomainException('Все позиции возвращены. Возврат не доступен');
        }
        return $refund;
    }


}
