<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\PriceHasMinimum;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Auth\Infrastructure\Models\Staff;
use App\Modules\Bank\Service\BankService;
use App\Modules\Cart\Domain\Entities\Cart;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Service\CouponService;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\OrderAwaitingMail;
use App\Modules\Notification\Events\TelegramHasReceived;
use App\Modules\Notification\Helpers\TelegramParams;
use App\Modules\Order\Application\Services\OrderLoggerService;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Events\OrderHasAwaiting;
use App\Modules\Order\Events\OrderHasCanceled;
use App\Modules\Order\Events\OrderHasSetManager;
use App\Modules\Order\Infrastructure\Events\OrderHasCreated;
use App\Modules\Order\Infrastructure\Models\Order;
use App\Modules\Order\Infrastructure\Models\OrderAddition;
use App\Modules\Order\Infrastructure\Models\OrderHistoryStatus;
use App\Modules\Order\Infrastructure\Models\OrderItem;
use App\Modules\Service\Report\InvoiceReport;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;


class OrderService
{

    private DeliveryService $deliveries;
    private Cart $cart;
    private ShopRepository $repository;
    private CouponService $coupons;
    private CalculatorOrder $calculator;
    private OrderLoggerService $logger;
    private MovementService $movementService;
    private OrderReserveService $reserveService;

    private \App\Modules\Setting\Entity\Coupon $coupon_set;
    private Parser $parser_set;
    private InvoiceReport $invoiceReport;
    private BankService $bankService;


    public function __construct(
        DeliveryService     $deliveries,
        Cart                $cart,
        ShopRepository      $repository,
        CouponService       $coupons,
        CalculatorOrder     $calculator,
        OrderLoggerService  $logger,
        MovementService     $movementService,
        OrderReserveService $reserveService,
        Settings            $settings,
        InvoiceReport       $invoiceReport,
        BankService         $bankService,

    )
    {
        $this->coupon_set = $settings->coupon;
        $this->parser_set = $settings->parser;
        $this->deliveries = $deliveries;
        $this->cart = $cart;
        $this->repository = $repository;
        $this->coupons = $coupons;
        $this->calculator = $calculator;
        $this->logger = $logger;
        $this->movementService = $movementService;
        $this->reserveService = $reserveService;
        $this->invoiceReport = $invoiceReport;
        $this->bankService = $bankService;
    }

    public function createOrder(int $client_id, string $type = Order::ONLINE): Order
    {
        $trader_id = Trader::default()->organization->id;
        return Order::register($client_id, $type, $trader_id);
    }

    //**** ФУНКЦИИ СОЗДАНИЯ ЗАКАЗА

    //TODO Где используется?, заменить!!
    public function coupon(string $code)
    {
        $coupon = $this->repository->getCoupon($code);
        if (!empty($coupon)) {
            $amountCart = ($this->cart->getCartToFront(0))['common']['amount'];
            $maxDiscount = round($amountCart * $this->coupon_set->coupon / 100);
            return min($coupon->bonus, $maxDiscount);
        }
        return 0;
    }


    //**** ФУНКЦИИ РАБОТЫ С ЗАКАЗОМ МЕНЕДЖЕРОМ

    public function setManager(Order $order, int $staff_id): void
    {
        $old = $order->staff_id == null ? '' : $order->staff->fullname->getFullName();

        $staff = Staff::find($staff_id);
        if (empty($staff)) throw new \DomainException('Менеджер под ID ' . $staff_id . ' не существует!');
        $order->setStatus(OrderHistoryStatus::DRAFT);
        $order->setManager($staff->id);
        $this->logger->log(orderId: $order->id, action: 'Назначен менеджер',
            value: $staff->fullname->getFullName(), old: $old);
        //if (is_null($order->lead->staff_id)) {

        event(new OrderHasSetManager($order));
        //}

    }

    /**
     * Отменить заказ
     */
    public function cancel(Order $order, string $comment): void
    {
        DB::transaction(function () use ($order, $comment) {
            $order->clearReserve();
            $order->setStatus(value: OrderHistoryStatus::CANCELLED, comment: $comment);

            foreach ($order->payments as $payment) {
                if ($payment->method != OrderPayment::METHOD_ACCOUNT)
                    throw new \DomainException('Есть платежи не по счету. Отмена только через Возврат');
                $payment->order_id = null;
                $payment->shopper_id = $order->shopper_id;
                $payment->trader_id = $order->trader_id;
                $payment->save();
            }
            event(new OrderHasCanceled($order));
            $this->logger->log(orderId: $order->id, action: 'Заказ отменен менеджером',
                object: $comment);

        });
    }

    /**
     * Отправить заказ на оплату - резерв, присвоение номера заказу, счет, услуги по сборке
     */
    public function awaiting(Order $order, Request $request): void
    {
        DB::transaction(function () use ($order, $request) {
            $emails = $request->input('emails', []);

            if ($order->status->value != OrderHistoryStatus::DRAFT) throw new \DomainException('Нельзя отправить заказ на оплату. Не верный статус');
            if ($order->getTotalAmount() == 0) throw new \DomainException('Сумма заказа не может быть равно нулю');

            $is_assemblage = false;
            $is_packing = false;
            foreach ($order->items as $item) {
                //Проверка, если у товара есть сборка и/или упаковка, то должна быть 1 услуга с таким типом
                if ($item->assemblage) $is_assemblage = true;
                if ($item->packing) $is_packing = true;

            }
            //Фиксируем цену за услугу
            foreach ($order->additions as $addition) {
                if ($is_packing && $addition->addition->type == Addition::PACKING) $is_packing = false;
                if ($is_assemblage && $addition->addition->type == Addition::ASSEMBLY) $is_assemblage = false;
                $addition->amount = $addition->getAmount();
                $addition->save();
            }
            if ($is_assemblage) throw new \DomainException('Не назначена услуга сборки');
            if ($is_packing) throw new \DomainException('Не назначена услуга упаковки');

            $order->setReserve(now()->addDays(3));
            $order->setNumber();
            $order->setStatus(OrderHistoryStatus::AWAITING);
            $order->refresh();
            $this->logger->log(orderId: $order->id, action: 'Заказ отправлен на оплату');

            //Пересоздать отчет и отправить письмо клиенту.
            //Создаем счет на оплату

            //    dd($request->all());
            if ($request->boolean('payment.account') || $request->boolean('payment.qr')) {
                $invoice = $request->boolean('payment.account') ? $this->invoiceReport->pdf($order) : null;
                //Создаем ссылку на оплату
                $link_payment = $request->boolean('payment.qr') ? $this->bankService->createPaymentLink($order) : null;
                SendSystemMail::dispatch(
                    $order->client,
                    new OrderAwaitingMail($order, $invoice, $link_payment),
                    Order::class,
                    $order->id,
                    $emails
                );
            }
            //TODO Или создаем событие //TODO event  Lead
            event(new OrderHasAwaiting($order));

        });
    }

    public function work(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if ($order->status->value != OrderHistoryStatus::AWAITING) throw new \DomainException('Заказ нельзя вернуть в работу');
            $order->status->delete();

            //Удаляем фиксацию цен на услугу
            foreach ($order->additions as $addition) {
                if (!$addition->addition->manual) {
                    $addition->amount = 0;
                    $addition->save();
                }
            }
            event(new OrderHasWork($order));
            $this->logger->log(orderId: $order->id, action: 'Заказ вернулся в работу');
        });

        //TODO event  Lead
    }

    /**
     * Установить новое время резерва
     */
    public function setReserveService(Order $order, Request $request): void
    {
        $old = $order->getReserveTo();
        $new_reserve = Carbon::parse($request->date('reserve_at'));
        $order->setReserve($new_reserve);
        $this->logger->log(orderId: $order->id, action: 'Новое время резерва',
            value: $request->string('reserve')->trim()->value(),
            old: $old->toString()
        );
    }

    //** ФУНКЦИИ РАБОТЫ С ЭЛЕМЕНТАМИ ЗАКАЗА

    /**
     * Добавить в заказ товар. НОВАЯ ВЕРСИЯ
     */
    public function addProduct(
        Order $order, int $product_id,
        float $quantity, bool $preorder = false,
        bool  $assemblage = false, bool $packing = false): void
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        $quantity_preorder = 0;
        //По предзаказу
        if ($preorder) {
            $quantity_preorder = $quantity;
            $quantity = 0;
        }

        if ($quantity > 0 && $product->getQuantitySell() <= $quantity) {
            $quantity_preorder = $quantity - $product->getQuantitySell(); //По предзаказу
            $quantity = $product->getQuantitySell(); //в наличии
        }

        $last_price = $product->getPrice(false, $order->client);
        if ($quantity > 0) {
            $orderItem = OrderItem::new($product->id, $quantity, false);
            if ($last_price == 0) throw new \DomainException('Нельзя добавить товар без цены ' . $product->name);
            $orderItem->setCost($last_price, $last_price);
            $orderItem->assemblage = $assemblage;
            $orderItem->packing = $packing;
            $order->items()->save($orderItem);
            //Товар в резерв, возвращаем новое время резерва
            $reserve_at = $this->reserveService->toReserve($orderItem, $quantity);
            //И устанавливаем его для всех товаров
            foreach ($order->items as $item) {
                foreach ($item->reserves as $reserve) {
                    $reserve->reserve_at = $reserve_at;
                    $reserve->save();
                }
            }
        }

        if ($quantity_preorder > 0) {
            $orderItemPre = OrderItem::new($product->id, $quantity_preorder, true);
            $pre_price = ($product->getPricePre() == 0) ? $last_price : $product->getPricePre();
            if ($last_price == 0) {
                //TODO Высчитываем цену по парсеру Доп.наценка и другое
                $parser = $product->parser;
                if (is_null($parser)) throw new \DomainException('Нельзя добавить товар без цены - ' . $product->name);
                $last_price = ceil($parser->price_sell * $product->brand->currency->fixed);
                $pre_price = $last_price;
            }
            $orderItemPre->setCost($last_price, $pre_price);
            $orderItemPre->assemblage = $assemblage;
            $orderItemPre->packing = $packing;
            $order->items()->save($orderItemPre);
        }

        $order->refresh();
        $this->recalculation($order);
        $this->logger->log(orderId: $order->id, action: 'Добавлен товар',
            object: $product->name, value: $quantity . ' шт.');
    }

    /**
     * Изменить данные о товаре
     */
    public function setItem(OrderItem $item, Request $request): void
    {
        DB::transaction(function () use ($item, $request) {
            $order = $item->order;
            $quantity = $request->float('quantity');
            $sell_cost = $request->integer('sell_cost');
            $percent = $request->float('percent');
            $comment = $request->string('comment')->trim()->value();
            $assemblage = $request->boolean('assemblage');
            $packing = $request->boolean('packing');

            if ($sell_cost > $item->base_cost) throw new \DomainException('Высокая продажная цена');
            if ($percent > 100 || $percent < 0) throw new \DomainException('Неверное значение скидки');

            //Изменения происходят только по одному полю
            ///*** 1. Изменилась Цена продажи или Процент скидки
            if ($item->getPercent() != $percent)
                $sell_cost = (int)ceil($item->base_cost - $item->base_cost * $percent / 100);
            if ($item->sell_cost != $sell_cost) {
                $old = $item->sell_cost;
                if ($item->product->getPriceMin() > $sell_cost) event(new PriceHasMinimum($item));
                $item->sell_cost = $sell_cost;
                $this->logger->log(orderId: $order->id, action: 'Изменена цена товара',
                    object: $item->product->name, value: price($sell_cost), old: $old);
            }
            ///*** 2. Изменилось Кол-во
            if ($item->quantity != $quantity) {
                $old = $item->quantity;
                $delta = $quantity - $item->quantity;
                if (!$item->preorder) { //Если не под заказ, то изменяем резерв
                    if ($delta > 0) {
                        $delta = min($item->product->getQuantitySell(), $delta);
                        $this->reserveService->upReserve($item, abs($delta));
                    } else {
                        $this->reserveService->downReserve($item, abs($delta));
                    }
                }
                $item->quantity += $delta;
                $this->logger->log(orderId: $order->id, action: 'Изменено кол-во товара',
                    object: $item->product->name, value: (string)$quantity . ' шт.', old: $old);
            }
            ///*** 3. Изменился комментарий
            if ($item->comment != $comment)
                $item->comment = $comment;
            ///*** 4. Изменилась сборка
            if ($item->assemblage != $assemblage) {
                $item->assemblage = $assemblage;
                $this->logger->log(orderId: $order->id, action: 'Изменена сборка товара',
                    object: $item->product->name, value: $assemblage ? 'Установлена' : 'Отменена');
            }
            ///*** 5. Изменилась упаковка
            if ($item->packing != $packing) {
                $item->packing = $packing;
                $this->logger->log(orderId: $order->id, action: 'Изменена упаковка товара',
                    object: $item->product->name, value: $packing ? 'Установлена' : 'Отменена');
            }
            $item->save();
            $order->refresh();
            $this->recalculation($order);
        });
    }

    /**
     * Удаляем позицию в заказе, пересчитываем резерв и скидки по заказу. НОВАЯ ВЕРСИЯ
     */
    public function deleteItem(OrderItem $item): void
    {
        $order = $item->order;
        $this->logger->log(orderId: $order->id, action: 'Удален товар из заказа',
            object: $item->product->name, value: (string)$item->quantity);
        foreach ($item->reserves as $reserve) {
            $reserve->delete();
        }

        $item->delete();
        $order->refresh();
        $this->recalculation($order);
    }

    //** СКИДКИ НА ВЕСЬ ЗАКАЗ

    /**
     * Пересчет заказа, расчет скидок на товар, по акциям и бонусам. Расчет скидки на заказ, по "Скидки"
     */
    private function recalculation(Order &$order): void
    {
        $order->refresh();
        /** @var OrderItem[] $items */
        $items = $order->items()->getModels();

        //Если заказ не через Парсер, ищем бонусы и скидки

            //Ищем акционные и бонусные товары
            /** @var OrderItem[] $items */
            $items = $this->calculator->calculate($items);
            foreach ($items as $item) {
                $item->save();
            }
            //Общие скидки, если имеется, фиксируем сумму (т.к. %% в будущем может измениться)
            if (!is_null($discount = $this->calculator->discount($items))) {
                $order->discount_id = $discount->id;
                $order->discount_amount = (int)ceil($order->getBaseAmount() * $discount->discount / 100);
            } else {
                $order->discount_id = null;
                $order->discount_amount = 0;
            }


        //Ручная скидка от скидок за товары
        $order->manual = 0;
        foreach ($order->items as $item) {
            if (is_null($item->discount_id)) {
                $order->manual += ($item->base_cost - $item->sell_cost) * $item->quantity;
            }
        }

        //Пересчет для купона
        if (!is_null($order->coupon_id)) {
            /** @var Coupon $coupon */
            $coupon = Coupon::find($order->coupon_id);
            $order->coupon_amount = $this->coupons->discount($coupon, $order);
        }
        $order->save();
    }

    //**** ФУНКЦИИ РАБОТЫ СО УСЛУГАМИ

    /**
     * Добавить в заказ доп.услугу
     */
    public function addAddition(Order $order, int $addition_id): OrderAddition
    {
        if (!is_null($order->getAddition($addition_id))) throw new \DomainException("Услуга уже добавлена");

        $orderAddition = OrderAddition::new($addition_id);
        $order->additions()->save($orderAddition);
        $orderAddition->refresh();
        $this->logger->log(orderId: $order->id, action: 'Добавлена услуга',
            object: $orderAddition->addition->name, value: price($orderAddition->getAmount()));
        return $orderAddition;
    }

    public function setAddition(OrderAddition $orderAddition, Request $request): void
    {
        $new_amount = $request->integer('amount');
        $new_quantity = $request->integer('quantity');
        $new_comment = $request->string('comment')->trim()->value();
        if ($orderAddition->addition->manual && $orderAddition->amount != $new_amount) {
            $this->logger->log(orderId: $orderAddition->order->id, action: 'Изменена Сумма услуги',
                object: $orderAddition->addition->name,
                value: $new_amount,
                old: $orderAddition->amount);
            $orderAddition->amount = $new_amount;
        }

        if ($orderAddition->addition->is_quantity && $orderAddition->quantity != $new_quantity){
            $this->logger->log(orderId: $orderAddition->order->id, action: 'Изменено Кол-во услуги',
                object: $orderAddition->addition->name,
                value: $new_quantity,
                old: $orderAddition->quantity);
            $orderAddition->quantity = $new_quantity;
        }

        if ($orderAddition->comment != $new_comment){
            $this->logger->log(orderId: $orderAddition->order->id, action: 'Изменено Коментарий услуги',
                object: $orderAddition->addition->name,
                value: $new_comment,
                old: $orderAddition->comment);
            $orderAddition->comment = $new_comment;
        }

        $orderAddition->save();
    }

    /**
     * Изменяем комментарий на услугу, возвращаем Заказ
     */
    public function addition_comment(OrderAddition $addition, string $comment): void
    {
        $addition->comment = $comment;
        $addition->save();
    }

    //**** ФУНКЦИИ ДРУГИЕ

    /**
     * Создать перемещение для заказа на основе резерва по складу отправки
     * LoggerOrder::class
     */
    public function movement(Order $order, int $storage_out, int $storage_in): MovementDocument
    {
        DB::transaction(function () use ($order, $storage_out, $storage_in, &$movement) {
            $movement = $this->movementService->create($storage_out, $storage_in);
            $movement->refresh();
            $order->movements()->attach($movement->id);

            foreach ($order->items as $item) {
                if (!is_null($reserve = $item->getReserveByStorage($storage_out))) {
                    if ($reserve->quantity > 0) {
                        $movement->addProduct($item->product_id, (float)$reserve->quantity, $item->id);
                    }
                }
            }
            $movement->refresh();
            $this->movementService->completed($movement);
            $this->logger->log(orderId: $order->id, action: 'Создано перемещение для заказа',
                value: $movement->storageOut->name . ' -> ' . $movement->storageIn->name,
                link: route('admin.accounting.movement.show', $movement)
            );
        });

        return $movement;
    }


    /**
     * Пересчет суммы для выдачи товара по распоряжению.
     * Остаток неизрасходованного лимита денег должен быть выше
     * стоимости товаров и услуг для нового распоряжения
     */
    #[ArrayShape(['remains' => "float", 'discount' => "float", 'expense' => "int", 'disable' => "bool"])]
    public function expenseCalculate(Order $order, string $_data): array
    {
        $remains = $order->getPaymentAmount() - $order->getExpenseAmount() + $order->getCoupon() + $order->getDiscountOrder();
        $data = json_decode($_data, true);
        $amount = 0;
        foreach ($data['items'] as $item) { //Суммируем по товарам
            $id_item = (int)$item['id'];
            $amount += $order->getItemById($id_item)->sell_cost * (float)$item['value'];
        }
        foreach ($data['additions'] as $addition) { //Суммируем по услугам
            $amount += (float)$addition['value'];
        }

        return [
            'remains' => price($remains),
            'expense' => price($amount),
            'discount' => price($order->getCoupon() + $order->getDiscountOrder()),
            'disable' => $amount > $remains || $amount == 0,
        ];
    }

    /**
     * Добавление товара в заказ, через Парсер
     * LoggerOrder::class
     */
    //TODO Переделать на получение полных данных из базы
    public function add_parser(Order $order, $search, float $quantity)
    {
        if (!$order->isParser()) throw new \DomainException('Заказ не под Парсер');
        $product = $this->parserService->findProduct($search);

        $cost_item = ceil($this->parser_set->parser_coefficient * $product->parser->price);
        $orderItemPre = OrderItem::new($product->id, $quantity, true);
        $orderItemPre->setCost((int)$cost_item, (int)$cost_item);

        $order->items()->save($orderItemPre);

        $order->refresh();
        $this->recalculation($order);
        $this->logger->log(orderId: $order->id, action: 'Добавлен товар через Парсер',
            object: $product->name, value: $quantity . ' шт.');
    }

    /**
     * Удаление услуги из заказа
     * LoggerOrder::class
     */
    public function deleteAddition(OrderAddition $addition): void
    {
       // if (!$addition->order->isManager()) throw new \DomainException('Нельзя удалить услугу');
        $this->logger->log(orderId: $addition->order->id, action: 'Удалена услуга ', object: $addition->addition->name,
            old: price($addition->getAmount()));
        $addition->delete();
    }

    /**
     * Установка скидки на Заказ
     * LoggerOrder::class
     */
    /*
    public function setDiscount(Order $order, Request $request): void
    {
        $code = $request->string('coupon')->trim()->value();
        $manual = $request->integer('manual');
        $percent = $request->float('percent');
        $action = $request->input('action');
        if ($action == 'coupon') {
            if (empty($code)) {
                if (!is_null($order->coupon_id)) {
                    $this->logger->log(orderId: $order->id, action: 'Удалена скидка по купону',
                        object: $order->coupon->code, old: $order->coupon->bonus);

                    $order->coupon_id = null;
                    $order->coupon_amount = 0;
                }
            } else {
                $coupon = $this->repository->getCoupon($code, $order->client_id);
                if (is_null($coupon)) throw new \DomainException('Неверный код купона');
                if ($coupon->started_at->gt(now())) throw new \DomainException('Купон еще не действует');
                if ($coupon->finished_at->lt(now())) throw new \DomainException('Купон уже не действует');
                $order->coupon_id = $coupon->id;
                $this->logger->log(orderId: $order->id, action: 'Скидка по купону',
                    object: 'Установлена',
                    value: $coupon->bonus);
            }

            $order->save();
            $this->recalculation($order);
        }

        if ($action == 'manual' || $action == 'percent') {

            $old_manual = (int)$order->manual;
            //Раскидываем скидку на все товары не из акций
            $base_amount = $order->getBaseAmountNotDiscount();
            if ($base_amount == 0) throw new \DomainException('В заказе нет товаров для установки ручной скидки');

            if ($action == 'manual') {
                $percent_item = $manual / $base_amount;

            } else {
                $percent_item = $percent / 100;
                $manual = (int)ceil($percent * $percent_item);
            }
            if ($percent_item > 1) throw new \DomainException('Скидка слишком высока');

            // $order->manual = $manual;

            foreach ($order->items as $item) {
                if (is_null($item->discount_id)) {
                    $sell_cost = ($item->base_cost * (1 - $percent_item));
                    if ($item->product->getPriceMin() > $sell_cost) event(new PriceHasMinimum($item));
                    $item->sell_cost = $sell_cost;
                    $item->save();
                }
            }
            //$order->save();
            $this->recalculation($order);
            $this->logger->log(orderId: $order->id, action: 'Установлена общая скидка',
                value: price($manual), old: price($old_manual));
        }

    }
*/
    public function setUser(Order $order, Request $request): void
    {
        $user = User::find($request->integer('user_id'));
        $order->client_id = $user->id;
        if (!is_null($user->organization)) $order->shopper_id = $user->organization->id;
        $order->save();
    }

    /**
     * Сменить организацию для выставления счета (покупателя и продавца)
     * LoggerOrder::class
     */
    public function setInfo(Order $order, Request $request): void
    {
        if ($order->trader_id != $request->integer('trader_id')) {
            $old = $order->trader->short_name;
            $order->trader_id = $request->integer('trader_id');
            $order->save();
            $order->refresh();
            $this->logger->log(orderId: $order->id, action: 'Изменена организация Продавец',
                value: $order->trader->short_name, old: $old);
            return;
        }

        if ($order->shopper_id != $request->input('shopper_id')) {
            $old = is_null($order->shopper) ? 'Физ.лицо' : $order->shopper->short_name;
            $order->shopper_id = $request->input('shopper_id');
            $order->save();
            $order->refresh();
            $this->logger->log(orderId: $order->id, action: 'Изменена организация Покупатель',
                value: is_null($order->shopper) ? 'Физ.лицо' : $order->shopper->short_name, old: $old);
            return;
        }
        $order->comment = $request->string('comment')->trim()->value();
        $this->logger->log(orderId: $order->id, action: 'Добавлен комментарий',
            value: $order->comment);
        $order->save();
    }

    /** Обрабатываем подтверждения из Телеграм */
    public function handle(TelegramHasReceived $event): void
    {
        if ($event->operation == TelegramParams::OPERATION_ORDER_TAKE) {
            /** @var Order $order */
            $order = Order::find($event->id);
            try {
                $order->setManager($event->staff->id);
                $order->setStatus(OrderHistoryStatus::DRAFT);
                //FIXME Отправка сообщений
                /*
                $event->staff->notify(
                    new StaffMessage(
                        NotificationHelper::EVENT_INFO,
                        'Принято!'
                    )
                );
                */
            } catch (\DomainException $e) {
                                //FIXME Отправка сообщений
                /*
                $event->staff->notify(
                    new StaffMessage(
                        NotificationHelper::EVENT_ERROR,
                        $e->getMessage()
                    )
                );
                */
            }

        }
    }

    public function setCreated(Order $order, $created): \Illuminate\Support\Carbon|Carbon
    {
        $order->created_at = $created ?? now();
        $order->save();
        return $order->created_at;
    }

    public function setComment(Order $order, Request $request): void
    {
        $old = $order->comment;
        $order->comment = $request->string('comment')->trim()->value();
        $this->logger->log(orderId: $order->id, action: 'Изменен комментарий',
            value: $order->comment, old: $old
        );
        $order->save();
    }

    public function setAssemblage(Request $request): void
    {
        $items = $request->input('items');
        $assemblage = $request->boolean('assemblage');

        foreach ($items as $item) {
            OrderItem::where('id', $item)->update(['assemblage' => $assemblage]);
        }

    }

    public function setPacking(Request $request): void
    {
        $items = $request->input('items');
        $packing = $request->boolean('packing');
        foreach ($items as $item) {
            OrderItem::where('id', $item)->update(['packing' => $packing]);
        }

    }


}
