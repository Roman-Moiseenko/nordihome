<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Entity\Admin;
use App\Entity\FullName;
use App\Entity\GeoAddress;
use App\Events\OrderHasCreated;
use App\Events\ThrowableHasAppeared;
use App\Events\UserHasCreated;
use App\Mail\VerifyMail;
use App\Modules\Admin\Entity\Options;
use App\Modules\Delivery\Entity\DeliveryOrder;
use App\Modules\Delivery\Entity\UserDelivery;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Service\CouponService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderResponsible;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Order\Entity\UserPayment;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\Cart\CartItem;
use App\Modules\Shop\CartItemInterface;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\ShopRepository;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;
use stdClass;

class OrderService
{

    private PaymentService $payments;
    private DeliveryService $deliveries;
    private Cart $cart;
    private int $coupon;
    private ShopRepository $repository;
    private CouponService $coupons;
    private $minutes;
    private ReserveService $reserves;
    private ParserCart $parserCart;
    private CalculatorOrder $calculator;

    public function __construct(
        PaymentService  $payments,
        DeliveryService $deliveries,
        Cart            $cart,
        ParserCart      $parserCart,
        ShopRepository  $repository,
        CouponService   $coupons,
        ReserveService  $reserves,
        CalculatorOrder $calculator
    )
    {
        $this->payments = $payments;
        $this->deliveries = $deliveries;
        $this->cart = $cart;
        $this->coupon = (new Options())->shop->coupon;
        $this->repository = $repository;
        $this->coupons = $coupons;
        $this->minutes = (new Options())->shop->reserve_order;
        $this->reserves = $reserves;
        $this->parserCart = $parserCart;
        $this->calculator = $calculator;
    }

    public function default_user_data(User $user = null): stdClass
    {
        /** @var User $user */
        if (is_null($user)) $user = Auth::guard('user')->user();
        $result = new stdClass();
        /** @var UserPayment payment */
        $result->payment = $this->payments->user($user->id);
        /** @var UserDelivery delivery */
        $result->delivery = $this->deliveries->user($user->id);
        $result->email = $user->email;
        $result->phone = $user->phone;
        $result->user = $user;
        return $result;
    }

    #[ArrayShape(['payment' => "array", 'delivery' => "array", 'phone' => "string", 'amount' => "array"])]
    public function checkorder(array $data): array
    {
        $enabled = true;
        $error = '';
        $default = $this->default_user_data();

        if (isset($data['payment'])) $default->payment->setPayment($data['payment']);
        if (isset($data['inn'])) {//Работа с ИНН. Проверяем изменился или новые данные, если да, загружаем по API
            $default->payment->setInvoice($data['inn']);
        }

        $default->delivery->setDeliveryType(isset($data['delivery']) ? (int)$data['delivery'] : null);

        if ($default->delivery->isRegion()) {
            $default->delivery->setDeliveryTransport(
                $data['company'] ?? '',
                GeoAddress::create(
                    $data['address-region'] ?? '',
                    $data['latitude-region'] ?? '',
                    $data['longitude-region'] ?? '',
                    $data['post-region'] ?? ''
                )
            );
        } else {
            $default->delivery->setDeliveryLocal(
                isset($data['storage']) ? (int)$data['storage'] : null,
                GeoAddress::create(
                    $data['address-local'] ?? '',
                    $data['latitude-local'] ?? '',
                    $data['longitude-local'] ?? '',
                    $data['post-local'] ?? ''
                )
            );
        }
        if (isset($data['fullname'])) {
            list ($surname, $firstname, $secondname) = explode(" ", $data['fullname']);
            //dd($surname);
            $default->delivery->setFullName(new FullName($surname, $firstname, $secondname));
        }

        if (isset($data['phone'])) {
            $default->user->update([
                'phone' => $data['phone']
            ]);
        }

        //Считаем стоимость доставки
        $items = null;
        if ($data['order'] == 'cart') $items = $this->cart->getItems();
        if ($data['order'] == 'parser') $items = $this->parserCart->getItems();

        $delivery_cost = $this->deliveries->calculate($default->delivery->user_id, $items);

        //TODO Сообщения об ошибках - неверное ИНН, Негабаритный груз для доставки (название продукта) $error

        if ($default->delivery->isStorage() && empty($default->delivery->storage)) $enabled = false;
        if ($default->delivery->isLocal() && empty($default->delivery->local->address)) $enabled = false;
        if (
            $default->delivery->isRegion() &&
            (empty($default->delivery->region->address) || empty($default->delivery->company))
        ) $enabled = false;
        $default = $this->default_user_data();
        return [
            'payment' => [
                'is_invoice' => $default->payment->isInvoice(),
                'invoice' => $default->payment->invoice(),
            ],
            'delivery' => [
                'delivery_local' => $default->delivery->local->address,
                'delivery_address' => $default->delivery->region->address,
                'company' => $default->delivery->company,
                'storage' => $default->delivery->storage,
                'fullname' => $default->delivery->fullname->getFullName(),
            ],
            'phone' => $default->phone,
            'amount' => [
                'delivery' => $delivery_cost,
                'caption' => $default->payment->online() ? 'Оплатить' : 'Оформить',
                'enabled' => $enabled,
                'error' => $error,
                //'amount' =>
            ],
        ];

    }

    public function coupon(string $code)
    {
        $coupon = $this->repository->getCoupon($code);
        if (!empty($coupon)) {
            $amountCart = ($this->cart->getCartToFront(0))['common']['amount'];
            $maxDiscount = round($amountCart * $this->coupon / 100);
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
        $default = $this->default_user_data();
        $OrderItems = $this->cart->getOrderItems();
        $order = Order::register($default->payment->user_id, Order::ONLINE, false);
        if ($request->has('code')) {
            $coupon = $this->repository->getCoupon($request->get('code'));
        } else {
            $coupon = null;
        }

        foreach ($OrderItems as $item) {
            $orderItem = $this->createItemToOrder($item, true);
            $order->items()->save($orderItem);
            $item->reserve = null; //Для очистки корзины, без затрагивания резерва
        }
        $this->cart->clearOrder(true);

        //Предзаказ
        if ($request['preorder'] == 1 && //В заказе установлена метка для предзаказа.
            !empty($PreOrderItems = $this->cart->getPreOrderItems())) {//и кол-во товаров не пусто
            foreach ($PreOrderItems as $item) {
                $orderItem = $this->createItemToOrder($item, false);
                $orderItem->preorder = true;
                $order->items()->save($orderItem);
            }
            $this->cart->clearPreOrder();
        }
        $this->updateFinance($order, $coupon);

        event(new OrderHasCreated($order));
        return $order;
    }

    /**
     * Создание заказа из корзины парсера клиента
     * @return Order
     */
    public function create_parser(): Order
    {
        $default = $this->default_user_data();
        $OrderItems = $this->parserCart->getItems();
        $order = Order::register($default->payment->user_id, Order::PARSER, true);
        $order->amount = $this->parserCart->amount;
        $order->total = $order->amount + $this->parserCart->delivery;
        $order->save();

        foreach ($OrderItems as $item) {
            $orderItem = $this->createItemToOrder($item, false);
            $order->items()->save($orderItem);
        }
        $this->parserCart->clear();

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
            $default = $this->default_user_data();
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
            $default = $this->default_user_data($user);
        }

        if (isset($request['payment'])) $default->payment->setPayment($request['payment']);
        if ($request['delivery'] == 'local') $default->delivery->setDeliveryType(DeliveryOrder::LOCAL);
        if ($request['delivery'] == 'region') $default->delivery->setDeliveryType(DeliveryOrder::REGION);
        $storage = null;
        if (is_numeric($request['delivery'])) {
            $default->delivery->setDeliveryType(DeliveryOrder::STORAGE);
            $storage = (int)$request['delivery'];
        }
        $Address = GeoAddress::create(
            $data['address'] ?? '',
            $data['latitude'] ?? '',
            $data['longitude'] ?? '',
            $data['post'] ?? ''
        );
        if ($default->delivery->isRegion()) {
            $default->delivery->setDeliveryTransport(DeliveryHelper::deliveries()[0]['class'], $Address);
        } else {
            $default->delivery->setDeliveryLocal($storage, $Address);
        }
        $product_id = $request['product_id'];
        /** @var Product $product */
        $product = Product::find($product_id);

        if (empty($product->lastPrice)) throw new \DomainException('Данный товар не подлежит продажи.');
        $order = Order::register($user->id, Order::ONLINE, true);

        $items[] = CartItem::create($product, 1, []);
        $items = $this->calculator->calculate($items);
        $orderItem = $this->createItemToOrder($items[0], true, $user->id);
        $order->items()->save($orderItem);

        $this->updateFinance($order);
        event(new OrderHasCreated($order));
        return $order;
    }

    /**
     * Создание заказа менеджером из Продаж
     * @param Request $request
     * @return void
     */
    public function create_sales(Request $request): Order
    {
        $data = json_decode($request['data'], true);
        $user_request = $data['user'];

        if (!isset($user_request['id'])) {//1. Пользователь новый.
            /// регистрируем его и отправляем ему письмо, со ссылкой верификации
            $password = Str::random(8);
            $user = User::register($user_request['email'], $password);
            $user->update(['phone' => $user_request['phone']]);
            event(new UserHasCreated($user));
        } else {//2. Пользователь старый.
            $user = User::find((int)$user_request['id']);
        }
        $default = $this->default_user_data($user);

        //Перезаполняем или заполняем данные
        if (isset($user_request['payment'])) $default->payment->setPayment($user_request['payment']);
        $default->delivery->setDeliveryType($user_request['delivery']);
        if ($default->delivery->isRegion()) {
            $default->delivery->setDeliveryTransport(
                DeliveryHelper::deliveries()[0]['class'],
                GeoAddress::create($user_request['region'], '', '', '')
            );
        } else {
            $storage = (int)$user_request['storage'] ?? null;
            $default->delivery->setDeliveryLocal(
                $storage,
                GeoAddress::create($user_request['local'], '', '', ''));
        }

        //Заказ создаем заказ, заполняем все товары
        $order = Order::register($user->id, Order::MANUAL, true);

        //Товары в наличии
        $orderFreeItems = array_map(function ($item) {
            $product = Product::find($item['id']);
            return CartItem::create($product, (int)$item['count'], []);
        }, $data['free']);
        $orderFreeItems = $this->calculator->calculate($orderFreeItems);
        foreach ($orderFreeItems as $item) {
            $itemOrder = $this->createItemToOrder($item, true, $user->id);
            $order->items()->save($itemOrder);
        }

        //Товары на заказ
        $orderPreItems = array_map(function ($item) {
            $product = Product::find($item['id']);
            return CartItem::create($product, (int)$item['count'], []);
        }, $data['preorder']);
        foreach ($orderPreItems as $item) {
            $itemOrder = $this->createItemToOrder($item, false);
            $itemOrder->preorder = true;
            $order->items()->save($itemOrder);
        }

        //Добавляем дополнительные услуги
        $additions_request = $data['additions'];
        foreach ($additions_request as $item) {
            $addition = OrderAddition::new((float)$item['amount'], (int)$item['purpose'], $item['comment']);
            $order->additions()->save($addition);
        }

        $this->updateFinance($order);
        event(new OrderHasCreated($order));

        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();

        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->responsible()->save(OrderResponsible::registerManager($staff->id));

        return $order;
    }

    private function createItemToOrder(CartItemInterface $item, bool $reserve, int $user_id = null): OrderItem
    {
        if ($reserve) {//Ставим в резерв
            if (is_null($reserveItem = $item->getReserve())) { //Не корзина, т.к. в элементе нет резерва
                $reserveItem = $this->reserves->toReserve(
                    $item->getProduct(),
                    $item->getQuantity(),
                    Reserve::TYPE_ORDER,
                    $this->minutes,
                    $user_id
                );
            } else { //корзина, товар уже в резерве, увеличиваем время
                $reserveItem->update([
                    'reserve_at' => now()->addMinutes($this->minutes),
                    'type' => Reserve::TYPE_ORDER,
                ]);
            }
        }

        $orderItem = new OrderItem();
        $orderItem->product_id = $item->getProduct()->id;
        $orderItem->quantity = $item->getQuantity();
        $orderItem->base_cost = $item->getBaseCost();
        $orderItem->sell_cost = $item->getSellCost();
        $orderItem->discount_id = $item->getDiscount();
        $orderItem->discount_type = $item->getDiscountType();
        $orderItem->options = $item->getOptions();
        $orderItem->reserve_id = ($reserve) ? $reserveItem->id : null;

        return $orderItem;
    }

    private function updateFinance(Order &$order, Coupon $coupon = null)
    {
        $base_amount = 0;
        $sell_amount = 0;
        foreach ($order->items as $item) {
            $base_amount += $item->base_cost;
            $sell_amount += $item->sell_cost;
        }
        $discount_coupon = $this->coupons->discount($coupon, $order);

        $order->setFinance(
            amount: $base_amount,
            discount: $base_amount - $sell_amount,
            coupon: $discount_coupon,
            coupon_id: is_null($coupon) ? null : $coupon->id);
    }


    #[Deprecated]
    public function payment(int $order_id, array $payment_data)
    {
        /** @var Order $order */
        $order = Order::find($order_id);


        foreach ($order->items as $item) {
            if ($item->reserve_id != null) {
                $item->reserve()->delete();
            } else {
                $order->setStatus(OrderStatus::REFUND);
                throw new \DomainException('Произведена оплата за отмененный заказ');
            }
        }
        $order->setStatus(OrderStatus::PAID);

        //TODO Создать платеж $payment_data

        //TODO Проводка покупки
    }
}
