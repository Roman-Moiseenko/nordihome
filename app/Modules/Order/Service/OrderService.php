<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Events\OrderHasAwaiting;
use App\Events\OrderHasCanceled;
use App\Events\OrderHasCreated;
use App\Events\OrderHasPaid;
use App\Events\OrderHasPrepaid;
use App\Events\PriceHasMinimum;
use App\Events\UserHasCreated;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\LoggerService;
use App\Modules\Base\Entity\GeoAddress;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Service\CouponService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\Parser\ParserService;
use App\Modules\Shop\Parser\ProductParser;
use App\Modules\Shop\ShopRepository;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;
use stdClass;

class OrderService
{

    private DeliveryService $deliveries;
    private Cart $cart;
    private int $coupon;
    private ShopRepository $repository;
    private CouponService $coupons;
    private ParserCart $parserCart;
    private CalculatorOrder $calculator;
    private LoggerService $logger;
    private MovementService $movementService;
    private OrderReserveService $reserveService;
    private ParserService $parserService;
    private stdClass $shop_options;


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
    )
    {
        $this->deliveries = $deliveries;
        $this->cart = $cart;
        $this->shop_options = (new Options())->shop;
        //$this->coupon = (new Options())->shop->coupon;
        $this->repository = $repository;
        $this->coupons = $coupons;
        // $this->minutes = (new Options())->shop->reserve_order;
        // $this->reserves = $reserves;
        $this->parserCart = $parserCart;
        $this->calculator = $calculator;
        $this->logger = $logger;
        $this->movementService = $movementService;
        $this->reserveService = $reserveService;
        $this->parserService = $parserService;
    }

    #[Deprecated]
    public function default_user_data(User $user = null): stdClass
    {
        /** @var User $user */
        if (is_null($user)) $user = Auth::guard('user')->user();
        $result = new stdClass();
        $result->payment = $user->payment;
        $result->delivery = $user->delivery;
        $result->email = $user->email;
        $result->phone = $user->phone;
        $result->user = $user;
        return $result;
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
            $maxDiscount = round($amountCart * $this->shop_options->coupon / 100);
            return min($coupon->bonus, $maxDiscount);
        }
        return 0;
    }

    /**
     * Создание заказа клиентом с Фронтенда
     * @param Request $request
     * @return Order
     */
    public function create(Request $request): Order
    {
        if (Auth::guard('user')->check()) {
            /** @var User $user */
            $user = Auth::guard('user')->user();
        } else {
            throw new \DomainException('Не задан клиент, проверка Заказа невозможна');
        }

        $order = Order::register($user->id, Order::ONLINE);
        if ($request->has('code')) {
            $coupon = $this->repository->getCoupon($request->get('code'));

            $order->coupon_id = $coupon->id;
            $order->save();
        }

        $items = ($request['preorder'] == 1) ? $this->cart->getItems() : $this->cart->getOrderItems();
        foreach ($items as $item) {
            if ($item->check) $this->add_product($order, $item->product->id, $item->quantity);
        }
        $this->cart->clearOrder();
        if ($request['preorder'] == 1) $this->cart->clearPreOrder();

        $order->refresh();
        $this->recalculation($order);

        event(new OrderHasCreated($order));
        return $order;
    }

    /**
     * Создание заказа из корзины парсера клиента
     * @return Order
     */
    public function create_parser(): Order
    {
        if (Auth::guard('user')->check()) {
            /** @var User $user */
            $user = Auth::guard('user')->user();
        } else {
            throw new \DomainException('Не задан клиент, проверка Заказа невозможна');
        }
        $OrderItems = $this->parserCart->getItems();
        $order = Order::register($user->id, Order::PARSER);
        $order->save();
        foreach ($OrderItems as $item) {
            $orderItemPre = OrderItem::new($item->product, $item->quantity, true, $order->user_id);
            $orderItemPre->base_cost = $item->cost;
            $orderItemPre->sell_cost = $item->cost;
            $order->items()->save($orderItemPre);
        }
        $this->parserCart->clear();
        $this->recalculation($order);
        event(new OrderHasCreated($order));
        return $order;
    }

    /**
     * Создание заказа по кнопке В 1 клик
     * @param Request $request
     * @return Order
     */
    public function create_click(Request $request)
    {
        if (Auth::guard('user')->check()) { //Проверяем клиент залогинен
            //$default = $this->default_user_data();
            $user = Auth::guard('user')->user();
        } else {
            $email = $request['email'];
            $phone = $request['phone'];

            $user = User::where('email', $email)->first();
            if (empty($user)) {
                $password = Str::random(8);
                $user = User::register($email, $password);
                $user->update(['phone' => $phone]);

                event(new UserHasCreated($user));
            }
            //$default = $this->default_user_data($user);
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
            $data['address'] ?? '',
            $data['latitude'] ?? '',
            $data['longitude'] ?? '',
            $data['post'] ?? ''
        );
        $user->address = $Address;
        $user->save();

        $product_id = $request['product_id'];
        /** @var Product $product */
        $product = Product::find($product_id);

        if ($product->getLastPrice($user->id) == 0) throw new \DomainException('Данный товар не подлежит продажи.');
        $order = Order::register($user->id, Order::ONLINE);

        $order = $this->add_product($order, $product->id, 1);
        $this->recalculation($order);
        event(new OrderHasCreated($order));
        return $order;
    }

    /**
     * Создание пустого заказа менеджером из Продаж
     * @param array $request
     * @return void
     */
    public function create_sales(array $request): Order
    {
        if (empty($request['user_id'])) {//1. Пользователь новый.
            $password = Str::random(8); /// регистрируем его и отправляем ему письмо, со ссылкой верификации
            $user = User::register($request['email'], $password);
            $user->setPhone($request['phone']);
            $user->setNameField(firstname: $request['name']);
            event(new UserHasCreated($user));
        } else {//2. Пользователь старый.
            $user = User::find((int)$request['user_id']);
            //Обновить Имя
            $user->setNameField(firstname: $request['name']);
        }
        if (isset($request['parser'])) {
            $order = Order::register($user->id, Order::PARSER); //Создаем пустой заказ
        } else {
            $order = Order::register($user->id, Order::MANUAL); //Создаем пустой заказ
        }
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($staff->id);
        $order->refresh();
        $this->logger->logOrder($order, 'Заказ создан менеджером', '', '');

        return $order;
    }


    //**** ФУНКЦИИ РАБОТЫ С ЗАКАЗОМ МЕНЕДЖЕРОМ

    public function setManager(Order $order, int $staff_id)
    {
        /** @var Admin $staff */
        $staff = Admin::find($staff_id);
        if (empty($staff)) throw new \DomainException('Менеджер под ID ' . $staff_id . ' не существует!');
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($staff->id);
        $this->logger->logOrder($order, 'Назначен менеджер', '', $staff->fullname->getFullName());
    }

    /**
     * Отменить заказ
     * @param Order $order
     * @param string $comment
     * @return void
     */
    public function canceled(Order $order, string $comment)
    {
        $order->clearReserve();
        $order->setStatus(value: OrderStatus::CANCEL, comment: $comment);
        event(new OrderHasCanceled($order));
        $this->logger->logOrder($order, 'Заказ отменен менеджером', '', $comment);
    }

    /**
     * Отправить заказ на оплату - резерв, присвоение номера заказу, счет, услуги по сборке
     * @param Order $order
     * @return void
     */
    public function setAwaiting(Order $order)
    {
        if ($order->status->value != OrderStatus::SET_MANAGER) throw new \DomainException('Нельзя отправить заказ на оплату. Не верный статус');
        if ($order->getTotalAmount() == 0)  throw new \DomainException('Сумма заказа не может быть равно нулю');

        foreach ($order->items as $item) {
            if ($item->assemblage == true) {
                $addition = OrderAddition::new($item->getAssemblage(), OrderAddition::PAY_ASSEMBLY, $item->product->name);
                $order->additions()->save($addition);
                $item->assemblage = false;
                $item->save();
            }
        }
        $order->setReserve(now()->addDays(3));
        $order->setNumber();
        $order->setStatus(OrderStatus::AWAITING);
        $order->refresh();
        $this->logger->logOrder($order, 'Заказ отправлен на оплату', '', '');

        event(new OrderHasAwaiting($order));


        flash('Заказ успешно создан! Ему присвоен номер ' . $order->number, 'success');
    }

    /**
     * Удалить заказ, если еще нет с ним работы
     * @param Order $order
     * @return void
     */
    public function destroy(Order $order)
    {
        if ($order->status->value == OrderStatus::FORMED) {
            $order->delete();
        } else {
            throw new \DomainException('Нельзя удалить заказ, который уже в работе');
        }
    }

    /**
     * Установить новое время резерва
     * @param Order $order
     * @param string $date
     * @param string $time
     * @return void
     */
    public function setReserveService(Order $order, string $date, string $time)
    {
        $new_reserve = $date . ' ' . $time . ':00';
        $order->setReserve(Carbon::parse($new_reserve));
        $this->logger->logOrder($order, 'Новое время резерва', '', $new_reserve);
    }


    //** ФУНКЦИИ РАБОТЫ С ЭЛЕМЕНТАМИ ЗАКАЗА

    /**
     * Добавить в заказ товар. НОВАЯ ВЕРСИЯ
     * @param Order $order
     * @param int $product_id
     * @param int $quantity
     * @return Order
     */
    public function add_product(Order $order, int $product_id, int $quantity): Order
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        $quantity_preorder = 0;
        if ($product->getCountSell() <= $quantity) {
            $quantity_preorder = $quantity - $product->getCountSell(); //По предзаказу
            $quantity = $product->getCountSell(); //в наличии
        }
        if ($quantity > 0) {
            $orderItem = OrderItem::new($product, $quantity, false, $order->user_id);
            $order->items()->save($orderItem);
            $this->reserveService->toReserve($orderItem, $quantity);
        }

        if ($quantity_preorder > 0) {
            $orderItemPre = OrderItem::new($product, $quantity_preorder, true, $order->user_id);
            $order->items()->save($orderItemPre);
        }
        $order->refresh();
        $this->recalculation($order);
        $this->logger->logOrder($order, 'Добавлен товар', $product->name, $quantity . ' шт.');

        return $order;
    }


    /**
     * Изменения кол-ва товара, с учетом "в наличии" и перерасчетом всего заказа. НОВАЯ ВЕРСИЯ.
     * @param OrderItem $item - изменяемая позиция
     * @param int $quantity - новое значение кол-ва
     * @return Order
     */
    public function update_quantity(OrderItem $item, int $quantity): Order
    {
        $delta = $quantity - $item->quantity;
        if ($delta == 0) return $item->order;

        if (!$item->preorder) {
            if ($delta > 0) {
                $delta = min($item->product->getCountSell(), $delta);
                $this->reserveService->upReserve($item, abs($delta));
            } else {
                $this->reserveService->downReserve($item, abs($delta));
            }
        }
        $item->quantity += $delta;
        $item->save();
        $order = $item->order;

        $this->recalculation($order);
        $order->refresh();
        $this->logger->logOrder($order, 'Изменено кол-во товара', $item->product->name, (string)$quantity . ' шт.');

        return $order;
    }

    /**
     * Изменяем цену продажи товара для текущего заказа
     * @param OrderItem $item
     * @param int $sell_cost
     * @return Order
     */
    public function update_sell(OrderItem $item, int $sell_cost): Order
    {
        $order = $item->order;

        if ($item->product->getPriceMin() > $sell_cost) {
            event(new PriceHasMinimum($item));
        }

        $item->sell_cost = $sell_cost;

        $item->save();
        $order->refresh();
        $this->recalculation($order);

        $this->logger->logOrder($order, 'Изменена цена товара', $item->product->name, price($sell_cost));
        return $order;
    }

    /**
     * Изменяем цену продажи товара в %% для текущего заказа
     * @param OrderItem $item
     * @param float $percent
     * @return Order
     */
    public function discount_item_percent(OrderItem $item, float $percent): Order
    {
        if ($percent > 100) throw new \DomainException('Скидка слишком высока');

        $sell_cost = (int)ceil($item->base_cost - $item->base_cost * $percent / 100);
        return $this->update_sell($item, $sell_cost); //ф-ция сохраняет лог
    }

    /**
     * Удаляем позицию в заказе, пересчитываем резерв и скидки по заказу. НОВАЯ ВЕРСИЯ
     * @param OrderItem $item
     * @return Order
     */
    public function delete_item(OrderItem $item): Order
    {
        $order = $item->order;
        $this->logger->logOrder($order, 'Удален товар из заказа', $item->product->name, (string)$item->quantity);
        foreach ($item->reserves as $reserve) {
            $reserve->delete();
        }

        $item->delete();
        $order->refresh();
        $this->recalculation($order);
        return $order;
    }

    public function update_item_comment(OrderItem $item, string $comment)
    {
        $item->comment = $comment;
        $item->save();
    }

    //** СКИДКИ НА ВЕСЬ ЗАКАЗ

    /**
     * Ручная скидка для Заказа в рублях
     * @param Order $order
     * @param float $discount
     * @return Order
     */
    public function discount_order(Order $order, float $discount): Order
    {
        $order->manual = $discount;
        $order->save();
        //Раскидываем скидку на все товары не из акций
        $base_amount = $order->getBaseAmountNotDiscount();
        if ($base_amount == 0) throw new \DomainException('В заказе нет товаров для установки ручной скидки');


        $percent_item = $discount / $base_amount;
        foreach ($order->items as $item) {
            if (is_null($item->discount_id)) {
                $sell_cost = (int)ceil($item->base_cost * (1 - $percent_item));
                if ($item->product->getPriceMin() > $sell_cost) event(new PriceHasMinimum($item));
                $item->sell_cost = $sell_cost;
                $item->save();
            }
        }
        $order->refresh();
        $this->logger->logOrder($order, 'Установлена общая скидка', '', price($discount));

        return $order;
    }

    /**
     * Ручная скидка для Заказа в %%
     * @param Order $order
     * @param float $discount_percent
     * @return Order
     */
    public function discount_order_percent(Order $order, float $discount_percent): Order
    {
        if ($discount_percent > 100) throw new \DomainException('Скидка слишком высока');
        $base_amount = $order->getBaseAmountNotDiscount();
        $discount = (int)ceil($discount_percent * $base_amount / 100);
        $this->logger->logOrder($order, 'Установлена общая скидка', 'в %%', $discount_percent);

        return $this->discount_order($order, $discount); //ф-ция имеет лог
    }

    /**
     * Пересчет заказа, расчет скидок на товар, по акциям и бонусам. Расчет скидки на заказ, по "Скидки"
     * @param Order $order
     * @return void
     */
    private function recalculation(Order &$order)
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
     * @param Order $order
     * @param array $request
     * @return Order
     */
    public function add_addition(Order $order, int $purpose, float $amount, string $comment): Order
    {
        if ($purpose == 0) throw new \DomainException('Не выбрана дополнительная услуга!');
        if ($amount == 0) throw new \DomainException('Стоимость услуги должна быть больше нуля!');
        $orderAddition = OrderAddition::new($amount, $purpose, $comment);
        $order->additions()->save($orderAddition);
        $order->refresh();
        $this->logger->logOrder($order, 'Добавлена услуга', $orderAddition->purposeHTML(), price($orderAddition->amount));
        return $order;
    }

    /**
     * Изменяем сумму на услугу, возвращаем Заказ
     * @param OrderAddition $addition
     * @param int $amount
     * @return Order
     */
    public function addition_amount(OrderAddition $addition, int $amount): Order
    {
        $addition->amount = $amount;
        $addition->save();
        $this->logger->logOrder($addition->order, 'Изменена цена услуги', $addition->purposeHTML(), price($amount));
        $addition->order->refresh();
        return $addition->order;
    }

    /**
     * Изменяем комментарий на услугу, возвращаем Заказ
     * @param OrderAddition $addition
     * @param string $comment
     * @return void
     */
    public function addition_comment(OrderAddition $addition, string $comment): void
    {
        $addition->comment = $comment;
        $addition->save();
    }

    /**
     * Удалить услугу, возвращает Заказ
     * @param OrderAddition $addition
     * @return Order
     */
    public function addition_delete(OrderAddition $addition): Order
    {
        $order = $addition->order;
        $this->logger->logOrder($order, 'Удалена услуга', $addition->purposeHTML(), price($addition->amount));
        $addition->delete();
        return $order;
    }

    /**
     * Изменение сборки для элемента заказа вкл/выкл
     * @param OrderItem $item
     * @return Order
     */
    public function check_assemblage(OrderItem $item): Order
    {
        $item->assemblage = !$item->assemblage;
        $item->save();
        $this->logger->logOrder($item->order, 'Изменена сборка', $item->product->name, ($item->assemblage) ? 'Добавлена' : 'Удалена');

        return $item->order;
    }

    //**** ФУНКЦИИ ДРУГИЕ
    /**
     * Создать перемещение для заказа на основе резерва по складу отправки
     * @param Order $order
     * @param int $storage_out
     * @param int $storage_in
     * @return MovementDocument
     */
    public function movement(Order $order, int $storage_out, int $storage_in): MovementDocument
    {
        $movement = $this->movementService->create([
            'storage_out' => $storage_out,
            'storage_in' => $storage_in,
        ]);
        $order->movements()->attach($movement->id);
        $movement->refresh();

        foreach ($order->items as $item) {
            if (!is_null($reserve = $item->getReserveByStorage($storage_out))) {
                if ($reserve->quantity > 0) {
                    $movement->addProduct($item->product, $reserve->quantity, $item->id);
                }
            }
        }
        $this->logger->logOrder(
            $order,
            'Создано перемещение для заказа',
            '',
            $movement->storageOut->name . ' -> ' . $movement->storageIn->name);

        return $movement;
    }

    public function update_comment(Order $order, string $comment): void
    {
        $this->logger->logOrder($order, 'Изменен комментарий', $order->comment, $comment);
        $order->comment = $comment;
        $order->save();
    }

    /**
     * Установить скидку по купону
     * @param Order $order
     * @param string $code
     * @return Order
     */
    public function set_coupon(Order $order, string $code)
    {
        if (empty($code)) {
            $order->coupon_id = null;
            $order->coupon_amount = 0;
        } else {
            $coupon = $this->repository->getCoupon($code, $order->user_id);
            if (is_null($coupon)) throw new \DomainException('Неверный код купона');
            if ($coupon->started_at->gt(now()))  throw new \DomainException('Купон еще не действует');
            if ($coupon->finished_at->lt(now()))  throw new \DomainException('Купон уже не действует');
            $order->coupon_id = $coupon->id;
        }
        $order->save();
        $order->refresh();
        $this->recalculation($order);
        $this->logger->logOrder($order, 'Скидка по купону', empty($code) ? 'Удалена' : 'Установлена',
            !empty($coupon) ? $coupon->bonus : '');

        return $order;
    }

    /**
     * Проверка Заказа после поступления оплаты, смена статуса, генерация события
     * @param Order $order
     * @return void
     */
    public function check_payment(Order $order)
    {
        if ($order->getTotalAmount() <= $order->getPaymentAmount()) {
            $order->setPaid();
            event(new OrderHasPaid($order));
        } else {
            event(new OrderHasPrepaid($order));
            if ($order->status->value == OrderStatus::AWAITING) {
                $order->setStatus(OrderStatus::PREPAID);
            }
        }
        //Если купон в заказе, то завершаем его использование
        if (!is_null($order->coupon_id) && $order->coupon->isNew()) {
            $order->coupon->completed();
        }
    }

    /**
     * Копируем заказ
     * @param Order $order
     * @return Order
     */
    public function copy(Order $order): Order
    {
        $new_order = $order->replicate();
        $new_order->created_at = Carbon::now();
        $new_order->number = null;
        $new_order->paid = false;
        $new_order->finished = false;
        $new_order->save();
        $new_order->statuses()->create(['value' => OrderStatus::FORMED]);
        $new_order->setStatus(OrderStatus::SET_MANAGER);

        //Перенос ручной скидки, отключен
        //$this->discount_order($new_order, $order->manual);

        $new_order->refresh();

        foreach ($order->items as $item) {
            $this->add_product($new_order, $item->product_id, $item->quantity);
        }

        //TODO Копировать Услуги
        foreach ($order->additions as $addition) {
            $this->add_addition($new_order, $addition->purpose, $addition->amount, $addition->comment);
        }

        $new_order->refresh();
        $this->logger->logOrder($new_order, 'Создан заказ копированием', '', $order->htmlNumDate());

        return $new_order;
    }

    /**
     * Пересчет суммы для выдачи товара по распоряжению.
     * Остаток неизрасходованного лимита денег должен быть выше
     * стоимости товаров и услуг для нового распоряжения
     * @param Order $order
     * @param string $_data
     * @return array
     */
    #[ArrayShape(['remains' => "float", 'discount'=> "float", 'expense' => "int", 'disable' => "bool"])]
    public function expenseCalculate(Order $order, string $_data): array
    {
        $remains = $order->getPaymentAmount() - $order->getExpenseAmount()+  $order->getCoupon() + $order->getDiscountOrder();
        $data = json_decode($_data, true);
        $amount = 0;

        foreach ($data['items'] as $item) { //Суммируем по товарам
            $id_item = (int)$item['id'];
            $amount += $order->getItemById($id_item)->sell_cost * (int)$item['value'];
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
     * @param Order $order
     * @param $search
     * @param int $quantity
     * @return void
     */
    public function add_parser(Order $order, $search, int $quantity)
    {
        if (!$order->isParser()) throw new \DomainException('Заказ не под Парсер');
        $product = $this->parserService->findProduct($search);

        $cost_item = ceil($this->shop_options->parser_coefficient * $product->parser->price);


        $orderItemPre = OrderItem::new($product, $quantity, true, $order->user_id);

        $orderItemPre->base_cost = (int)$cost_item;
        $orderItemPre->sell_cost = (int)$cost_item;

        $order->items()->save($orderItemPre);

        $order->refresh();
        $this->recalculation($order);
        $this->logger->logOrder($order, 'Добавлен товар через Парсер', $product->name, $quantity . ' шт.');

        //return $order;

    }
}
