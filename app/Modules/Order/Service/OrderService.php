<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasCanceled;
use App\Events\OrderHasCreated;
use App\Events\OrderHasPaid;
use App\Events\OrderHasPrepaid;
use App\Events\PriceHasMinimum;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Analytics\LoggerService;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Service\CouponService;
use App\Modules\Guide\Entity\Addition;
use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\OrderAwaitingMail;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderPayment;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Product\Entity\Product;
use App\Modules\Service\Report\InvoiceReport;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Repository\ShopRepository;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;


class OrderService
{

    private DeliveryService $deliveries;
    private Cart $cart;
    private ShopRepository $repository;
    private CouponService $coupons;
    private ParserCart $parserCart;
    private CalculatorOrder $calculator;
    private LoggerService $logger;
    private MovementService $movementService;
    private OrderReserveService $reserveService;
    private ParserService $parserService;

    private \App\Modules\Setting\Entity\Coupon $coupon_set;
    private Parser $parser_set;
    private InvoiceReport $invoiceReport;

    public function __construct(
        DeliveryService     $deliveries,
        Cart                $cart,
        ParserCart          $parserCart,
        ParserService       $parserService,
        ShopRepository      $repository,
        CouponService       $coupons,
        CalculatorOrder     $calculator,
        LoggerService       $logger,
        MovementService     $movementService,
        OrderReserveService $reserveService,
        SettingRepository   $settings,
        InvoiceReport       $invoiceReport, //
    )
    {
        $this->coupon_set = $settings->getCoupon();
        $this->parser_set = $settings->getParser();
        $this->deliveries = $deliveries;
        $this->cart = $cart;
        $this->repository = $repository;
        $this->coupons = $coupons;
        $this->parserCart = $parserCart;
        $this->calculator = $calculator;
        $this->logger = $logger;
        $this->movementService = $movementService;
        $this->reserveService = $reserveService;
        $this->parserService = $parserService;
        $this->invoiceReport = $invoiceReport;
    }

    private function createOrder(int $user_id = null, int $type = Order::ONLINE): Order
    {
        $trader_id = Trader::default()->organization->id;
        return Order::register($user_id, $type, $trader_id);
    }

    //**** ФУНКЦИИ СОЗДАНИЯ ЗАКАЗА
    #[ArrayShape(['payment' => "array", 'delivery' => "array", 'phone' => "string", 'amount' => "array"])]
    public function checkorder(array $data): array
    {
        $enabled = true;
        $error = '';
        if (Auth::guard('user')->check()) {
            /** @var User $user */
            $user = Auth::guard('user')->user();
        } else {
            throw new \DomainException('Не задан клиент, проверка Заказа невозможна');
        }

        if (isset($data['payment'])) $user->payment->setPayment($data['payment']);
        if (isset($data['inn'])) {//Работа с ИНН. Проверяем изменился или новые данные, если да, загружаем по API
            $user->payment->setInvoice($data['inn']);
        }

        $user->delivery = (isset($data['delivery']) ? (int)$data['delivery'] : null);

        if ($user->delivery == OrderExpense::DELIVERY_REGION) {
            $user->address->address = $data['address-region'] ?? '';
            $user->address->post = $data['post-region'] ?? '';
            $user->address->latitude = $data['latitude-region'] ?? '';
            $user->address->longitude = $data['longitude-region'] ?? '';
            $user->save();

        } else {
            $user->address->address = $data['address-local'] ?? '';
            $user->address->post = $data['post-local'] ?? '';
            $user->address->latitude = $data['latitude-local'] ?? '';
            $user->address->longitude = $data['longitude-local'] ?? '';
            $user->save();
        }
        if (isset($data['fullname'])) {
            list ($surname, $firstname, $secondname) = explode(" ", $data['fullname']);
            //$user->fullname = FullName::create(params: $data['fullname']);
            $user->setNameField($surname, $firstname, $secondname);
        }

        if (isset($data['phone'])) {
            $user->update([
                'phone' => $data['phone']
            ]);
        }

        //Считаем стоимость доставки
        $items = null;
        if ($data['order'] == 'cart') $items = $this->cart->getItems();
        if ($data['order'] == 'parser') $items = $this->parserCart->getItems();

        $delivery_cost = $this->deliveries->calculate($user->id, $items);

        //TODO Сообщения об ошибках - неверное ИНН, Негабаритный груз для доставки (название продукта) $error
        if ($user->isStorage()) $user->storage = $data['storage'] ?? null;
        $user->save();
        if ($user->delivery == OrderExpense::DELIVERY_STORAGE && empty($user->StorageDefault())) $enabled = false;
        if ($user->delivery != OrderExpense::DELIVERY_STORAGE && empty($user->address->address)) $enabled = false;

        $user->refresh();
        return [
            'payment' => [
                'is_invoice' => $user->payment->isInvoice(),
                'invoice' => $user->payment->invoice(),
            ],
            'delivery' => [
                'delivery_local' => $user->address->address,
                'delivery_address' => $user->address->address,
                'company' => '', //$user->delivery->company,
                'storage' => $user->StorageDefault(),
                'fullname' => $user->fullname->getFullName(),
            ],
            'phone' => phone($user->phone),
            'amount' => [
                'delivery' => $delivery_cost,
                'caption' => $user->payment->online() ? 'Оплатить' : 'Оформить',
                'enabled' => $enabled,
                'error' => $error,
                //'amount' =>
            ],
        ];

    }

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

    /**
     * Создание заказа клиентом с Фронтенда
     */
    public function create(Request $request): Order
    {
        DB::transaction(function () use ($request, &$order) {
            if (Auth::guard('user')->check()) {
                /** @var User $user */
                $user = Auth::guard('user')->user();
            } else {
                throw new \DomainException('Не задан клиент, проверка Заказа невозможна');
            }

            $order = $this->createOrder($user->id);
            if ($request->has('code')) {
                $coupon = $this->repository->getCoupon($request->get('code'));
                $order->coupon_id = $coupon->id;
                $order->save();
            }

            $items = ($request['preorder'] == 1) ? $this->cart->getItems() : $this->cart->getOrderItems();
            foreach ($items as $item) {
                if ($item->check) $this->addProduct($order, $item->product->id, $item->quantity);
            }
            $this->cart->clearOrder();
            if ($request['preorder'] == 1) $this->cart->clearPreOrder();

            $order->refresh();
            $this->recalculation($order);

            event(new OrderHasCreated($order));

        });
        return $order;
    }

    /**
     * Создание заказа из корзины парсера клиента
     */
    //TODO Переделать на получение полных данных из базы
    public function create_parser(): Order
    {
        DB::transaction(function () use (&$order) {
            if (Auth::guard('user')->check()) {
                /** @var User $user */
                $user = Auth::guard('user')->user();
            } else {
                throw new \DomainException('Не задан клиент, проверка Заказа невозможна');
            }
            $OrderItems = $this->parserCart->getItems();
            $order = $this->createOrder($user->id);
            $order->save();
            foreach ($OrderItems as $item) {
                $orderItemPre = OrderItem::new($item->product, $item->quantity, true);
                $orderItemPre->setCost($item->cost, $item->cost);
                $order->items()->save($orderItemPre);
            }
            $this->parserCart->clear();
            $this->recalculation($order);
            event(new OrderHasCreated($order));
        });
        return $order;
    }

    /**
     * Создание заказа по кнопке В 1 клик
     */
    public function create_click(Request $request)
    {
        DB::transaction(function () use ($request, &$order) {
            if (Auth::guard('user')->check()) { //Проверяем клиент залогинен
                $user = Auth::guard('user')->user();
            } else {
                $email = $request->string('email')->trim()->value();
                $user = User::where('email', $email)->first();
                if (empty($user)) {
                    $password = Str::random(8);
                    $user = User::register($email, $password);
                    $user->setPhone(phoneToDB($request->string('phone')));
                    //event(new UserHasCreated($user));
                }
            }

            if (isset($request['payment'])) $user->payment->setPayment($request['payment']);
            if ($request['delivery'] == 'local') $user->delivery = OrderExpense::DELIVERY_LOCAL;
            if ($request['delivery'] == 'region') $user->delivery = OrderExpense::DELIVERY_REGION;
            $storage = null;
            if (is_numeric($request['delivery'])) {
                $user->delivery = OrderExpense::DELIVERY_STORAGE;
                $storage = (int)$request['delivery'];
            }
            $Address = GeoAddress::create(
                $request->string('address')->trim()->value(),
                $request->string('latitude')->trim()->value(),
                $request->string('longitude')->trim()->value(),
                $request->string('post')->trim()->value()
            );
            $user->address = $Address;
            $user->save();

            $product_id = $request->integer('product_id');
            /** @var Product $product */
            $product = Product::find($product_id);
            if (is_null($product)) throw new \DomainException('Данный товар не найден');
            if ($product->getPrice(false, $user) == 0) throw new \DomainException('Данный товар не подлежит продажи.');
            $order = $this->createOrder($user->id);

            $this->addProduct($order, $product->id, 1);
            $this->recalculation($order);
            event(new OrderHasCreated($order));
        });
        return $order;
    }

    /**
     * Создание пустого заказа менеджером из Продаж
     */
    public function create_sales(): Order
    {
        DB::transaction(function () use (&$order) {
            $order = $this->createOrder(type: Order::MANUAL); //Создаем пустой заказ

            /** @var Admin $staff */
            $staff = Auth::guard('admin')->user();
            $order->setStatus(OrderStatus::SET_MANAGER);
            $order->setManager($staff->id);
            $order->refresh();
            $this->logger->logOrder($order, 'Заказ создан менеджером',
                '', '', null);
        });
        return $order;
    }


    //**** ФУНКЦИИ РАБОТЫ С ЗАКАЗОМ МЕНЕДЖЕРОМ

    public function setManager(Order $order, int $staff_id): void
    {
        /** @var Admin $staff */
        $staff = Admin::find($staff_id);
        if (empty($staff)) throw new \DomainException('Менеджер под ID ' . $staff_id . ' не существует!');
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($staff->id);
        $this->logger->logOrder($order, 'Назначен менеджер',
            '', $staff->fullname->getFullName(), null);
    }

    /**
     * Отменить заказ
     */
    public function cancel(Order $order, string $comment): void
    {
        DB::transaction(function () use ($order, $comment) {
            $order->clearReserve();
            $order->setStatus(value: OrderStatus::CANCEL, comment: $comment);

            foreach ($order->payments as $payment) {
                if ($payment->method != OrderPayment::METHOD_ACCOUNT)
                    throw new \DomainException('Есть платежи не по счету. Отмена только через Возврат');
                $payment->order_id = null;
                $payment->shopper_id = $order->shopper_id;
                $payment->trader_id = $order->trader_id;
                $payment->save();
            }
            event(new OrderHasCanceled($order));
            $this->logger->logOrder($order, 'Заказ отменен менеджером',
                '', $comment, null);

        });
    }

    /**
     * Отправить заказ на оплату - резерв, присвоение номера заказу, счет, услуги по сборке
     */
    public function awaiting(Order $order, array $emails): void
    {
        DB::transaction(function () use ($order, $emails) {
            if ($order->status->value != OrderStatus::SET_MANAGER) throw new \DomainException('Нельзя отправить заказ на оплату. Не верный статус');
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
            $order->setStatus(OrderStatus::AWAITING);
            $order->refresh();
            $this->logger->logOrder($order, 'Заказ отправлен на оплату',
                '', '', null);

            //Пересоздать отчет и отправить письмо клиенту
            $invoice = $this->invoiceReport->xlsx($order);
            SendSystemMail::dispatch(
                $order->user,
                new OrderAwaitingMail($order, $invoice),
                Order::class,
                $order->id,
                $emails
            );

            //TODO Или создаем событие
            // event(new OrderHasAwaiting($order));

            //flash('Заказ успешно создан! Ему присвоен номер ' . $order->number, 'success');
        });
    }


    public function work(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if ($order->status->value != OrderStatus::AWAITING) throw new \DomainException('Заказ нельзя вернуть в работу');
            $order->status->delete();

            //Удаляем фиксацию цен на услугу
            foreach ($order->additions as $addition) {
                if (!$addition->addition->manual) {
                    $addition->amount = 0;
                    $addition->save();
                }
            }
            $this->logger->logOrder($order, 'Заказ вернулся в работу',
                '', '', null);
        });
    }

    /**
     * Установить новое время резерва
     */
    public function setReserveService(Order $order, Request $request): void
    {
        $new_reserve = Carbon::parse($request->date('reserve_at'));
        $order->setReserve($new_reserve);
        $this->logger->logOrder($order, 'Новое время резерва',
            '', $request->string('reserve')->trim()->value(), null);
    }

    //** ФУНКЦИИ РАБОТЫ С ЭЛЕМЕНТАМИ ЗАКАЗА

    /**
     * Добавить в заказ товар. НОВАЯ ВЕРСИЯ
     */
    public function addProduct(
        Order $order, int $product_id,
        float $quantity, bool $preorder = false,
        bool $assemblage = false, bool $packing = false): void
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

        $last_price = $product->getPrice(false, $order->user);
        if ($quantity > 0) {
            $orderItem = OrderItem::new($product, $quantity, false);
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
            $orderItemPre = OrderItem::new($product, $quantity_preorder, true);
            $pre_price = ($product->getPricePre() == 0) ? $last_price : $product->getPricePre();
            if ($last_price == 0) {
                //TODO Высчитываем цену по парсеру Доп.наценка и другое
                $parser = $product->parser;
                if (is_null($parser)) throw new \DomainException('Нельзя добавить товар без цены - ' . $product->name);

                $last_price = ceil($parser->price_sell * $product->brand->currency->exchange * (1  + $product->brand->currency->extra / 100));
                //$parser->price_sell;
                $pre_price = $last_price;
            }
            $orderItemPre->setCost($last_price, $pre_price);
            $orderItemPre->assemblage = $assemblage;
            $orderItemPre->packing = $packing;
            $order->items()->save($orderItemPre);
        }

        $order->refresh();
        $this->recalculation($order);
        $this->logger->logOrder($order, 'Добавлен товар',
            $product->name, $quantity . ' шт.', null);
    }

    public function addProducts(Order $order, array $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($order,
                $product['product_id'],
                $product['quantity'],
            );
        }
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
                if ($item->product->getPriceMin() > $sell_cost) event(new PriceHasMinimum($item));
                $item->sell_cost = $sell_cost;
                $this->logger->logOrder($order, 'Изменена цена товара',
                    $item->product->name, price($sell_cost), null);
            }
            ///*** 2. Изменилось Кол-во
            if ($item->quantity != $quantity) {
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
                $this->logger->logOrder($order, 'Изменено кол-во товара',
                    $item->product->name, (string)$quantity . ' шт.', null);
            }
            ///*** 3. Изменился комментарий
            if ($item->comment != $comment)
                $item->comment = $comment;
            ///*** 4. Изменилась сборка
            if ($item->assemblage != $assemblage) {
                $item->assemblage = $assemblage;
                $this->logger->logOrder($order, 'Изменена сборка товара',
                    $item->product->name, $assemblage ? 'Установлена' : 'Отменена', null);
            }
            ///*** 5. Изменилась упаковка
            if ($item->packing != $packing) {
                $item->packing = $packing;
                $this->logger->logOrder($order, 'Изменена упаковка товара',
                    $item->product->name, $packing ? 'Установлена' : 'Отменена', null);
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
        $this->logger->logOrder($order, 'Удален товар из заказа',
            $item->product->name, (string)$item->quantity, null);
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
        if (!$order->isParser()) {
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
        $orderAddition = OrderAddition::new($addition_id);
        $order->additions()->save($orderAddition);
        $orderAddition->refresh();
        $this->logger->logOrder($order, 'Добавлена услуга',
            $orderAddition->addition->name, price($orderAddition->getAmount()), null);
        return $orderAddition;
    }

    public function setAddition(OrderAddition $orderAddition, Request $request): void
    {
        if ($orderAddition->addition->manual) {
            $orderAddition->amount = $request->integer('amount');
        }
        $orderAddition->comment = $request->string('comment')->trim()->value();
        if ($orderAddition->addition->is_quantity) $orderAddition->quantity = $request->integer('quantity');
        $orderAddition->save();
        $this->logger->logOrder($orderAddition->order, 'Изменена услуга',
            $orderAddition->addition->name,
            json_encode(['Сумма' => $orderAddition->amount, 'Кол-во' => $orderAddition->quantity, 'Комментарий' => $orderAddition->comment]), null);
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
            $this->logger->logOrder($order, 'Создано перемещение для заказа',
                '',
                $movement->storageOut->name . ' -> ' . $movement->storageIn->name,
                route('admin.accounting.movement.show', $movement)
            );
        });

        return $movement;
    }

    public function update_comment(Order $order, string $comment): void
    {
        $this->logger->logOrder($order, 'Изменен комментарий',
            $order->comment, $comment, null);
        $order->comment = $comment;
        $order->save();
    }

    /**
     * Проверка Заказа после поступления/отмены оплаты, смена статуса, генерация события
     */
    public function checkPayment(Order $order): void
    {
        $payment = $order->getPaymentAmount();
        $total = $order->getTotalAmount();
        //Заказ в ожидании
        if ($order->status->value == OrderStatus::AWAITING) {
            $order->setReserve(now()->addDays(45));//Увеличиваем резерв
            if ($payment == $total) { //Оплачен полностью
                $order->setPaid();
                event(new OrderHasPaid($order));
            }
            if ($payment < $total) { //Оплачен частично
                $order->setStatus(OrderStatus::PREPAID);
                event(new OrderHasPrepaid($order));
            }
        }

        //Заказ на предоплате
        if ($order->status->value == OrderStatus::PREPAID) {
            if ($payment == $total) { //Оплачен полностью
                $order->setPaid();
                $order->setReserve(now()->addDays(45));//Увеличиваем резерв
                event(new OrderHasPaid($order));
            } else {
                if ($payment == 0) { //Отмена предоплаты
                    $order->delStatus(OrderStatus::PREPAID);
                } else { //Новая предоплата
                    $order->setReserve(now()->addDays(45));//Увеличиваем резерв
                    event(new OrderHasPrepaid($order));
                }

            }
        }
        if ($order->status->value == OrderStatus::PAID) {
            if ($payment == $total) throw new \DomainException('Неверный вызов checkPayment');
            if ($payment == 0) { //Полная отмена оплаты
                $order->delStatus(OrderStatus::PAID);
                $order->delStatus(OrderStatus::PREPAID);
            } else {
                $order->delStatus(OrderStatus::PAID);
                $order->refresh();
                if ($order->status->value != OrderStatus::PREPAID) $order->setStatus(OrderStatus::PREPAID);
            }
        }

        //Если купон в заказе, то завершаем его использование
        if (!is_null($order->coupon_id) && $order->coupon->isNew()) {
            $order->coupon->completed();
        }
    }

    /**
     * Копируем заказ
     * LoggerOrder::class
     */
    public function copy(Order $order): Order
    {
        //TODO Переделать под новые данные (+ организации, упаковка/сборка товара, кол-во и цена услуг)
        DB::transaction(function () use ($order, &$new_order) {
            $new_order = $order->replicate();

            $new_order->created_at = Carbon::now();
            $new_order->number = null;
            $new_order->paid = false;
            $new_order->finished = false;
            $new_order->save();
            $new_order->statuses()->create(['value' => OrderStatus::FORMED]);
            $new_order->setStatus(OrderStatus::SET_MANAGER);
            $new_order->refresh();

            foreach ($order->items as $item) {
                if ($item->product->isSale()) {
                    //dd($item->assemblage);

                    $this->addProduct($new_order, $item->product_id, $item->quantity, $item->preorder, $item->assemblage, $item->packing);
                }
            }

            foreach ($order->additions as $addition) {
                $orderAddition = $this->addAddition($new_order, $addition->addition_id);
                if ($addition->addition->is_quantity) $orderAddition->quantity = $addition->quantity;
                if ($addition->addition->manual) $orderAddition->amount = $addition->amount;
                $orderAddition->save();
            }

            $new_order->refresh();
            $this->logger->logOrder($new_order, 'Создан заказ копированием',
                '', $order->htmlNumDate(), null);
        });

        return $new_order;
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
        $orderItemPre = OrderItem::new($product, $quantity, true);
        $orderItemPre->setCost((int)$cost_item, (int)$cost_item);

        $order->items()->save($orderItemPre);

        $order->refresh();
        $this->recalculation($order);
        $this->logger->logOrder($order, 'Добавлен товар через Парсер',
            $product->name, $quantity . ' шт.', null);
    }

    /**
     * Удаление услуги из заказа
     * LoggerOrder::class
     */
    public function deleteAddition(OrderAddition $addition): void
    {
        if (!$addition->order->isManager()) throw new \DomainException('Нельзя удалить услугу');
        $this->logger->logOrder($addition->order, 'Удалена услуга',
            $addition->addition->name, price($addition->getAmount()), null);
        $addition->delete();
    }

    /**
     * Установка скидки на Заказ
     * LoggerOrder::class
     */
    public function setDiscount(Order $order, Request $request): void
    {
        $code = $request->string('coupon')->trim()->value();
        $manual = $request->integer('manual');
        $percent = $request->float('percent');
        $action = $request->input('action');
        if ($action == 'coupon') {
            if (empty($code)) {
                $order->coupon_id = null;
                $order->coupon_amount = 0;
            } else {
                $coupon = $this->repository->getCoupon($code, $order->user_id);
                if (is_null($coupon)) throw new \DomainException('Неверный код купона');
                if ($coupon->started_at->gt(now())) throw new \DomainException('Купон еще не действует');
                if ($coupon->finished_at->lt(now())) throw new \DomainException('Купон уже не действует');
                $order->coupon_id = $coupon->id;
            }
            $order->save();
            $this->recalculation($order);
            $this->logger->logOrder($order, 'Скидка по купону',
                empty($code) ? 'Удалена' : 'Установлена',
                !empty($coupon) ? $coupon->bonus : '', null);
        }

        if ($action == 'manual' || $action == 'percent') {

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
            $this->logger->logOrder($order, 'Установлена общая скидка',
                '', price($manual), null);
        }

    }

    public function setUser(Order $order, Request $request): void
    {
        $user = User::find($request->integer('user_id'));
        $order->user_id = $user->id;
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
            $this->logger->logOrder($order, 'Изменена организация Продавец',
                $old, $order->trader->short_name,
            null);
            return;
        }

        if ($order->shopper_id != $request->input('shopper_id')) {
            $old = is_null($order->shopper) ? 'Физ.лицо' : $order->shopper->short_name;
            $order->shopper_id = $request->input('shopper_id');
            $order->save();
            $order->refresh();
            $this->logger->logOrder($order, 'Изменена организация Покупатель',
                $old, is_null($order->shopper) ? 'Физ.лицо' : $order->shopper->short_name, null);
            return;
        }
        $order->comment = $request->string('comment')->trim()->value();
        $this->logger->logOrder($order, 'Добавлен комментарий',
            '', $order->comment, null);
        $order->save();
    }


}
