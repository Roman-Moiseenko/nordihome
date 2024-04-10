<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Entity\Admin;
use App\Entity\FullName;
use App\Entity\GeoAddress;
use App\Events\OrderHasCreated;
use App\Events\UserHasCreated;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\Entity\LoggerOrder;
use App\Modules\Analytics\LoggerService;
use App\Modules\Delivery\Entity\DeliveryOrder;
use App\Modules\Delivery\Entity\UserDelivery;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Delivery\Service\DeliveryService;
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
use App\Modules\Shop\CartItemInterface;
use App\Modules\Shop\Parser\ParserCart;
use App\Modules\Shop\ShopRepository;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    private $minutes; //Время резерва товара в заказе
    private ReserveService $reserves;
    private ParserCart $parserCart;
    private CalculatorOrder $calculator;
    private LoggerService $logger;

    public function __construct(
        PaymentService  $payments,
        DeliveryService $deliveries,
        Cart            $cart,
        ParserCart      $parserCart,
        ShopRepository  $repository,
        CouponService   $coupons,
        ReserveService  $reserves,
        CalculatorOrder $calculator,
        LoggerService   $logger,
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
        $this->logger = $logger;
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
        $order = Order::register($default->payment->user_id, Order::ONLINE);
        if ($request->has('code')) {
            $coupon = $this->repository->getCoupon($request->get('code'));
            $discount_coupon = $this->coupons->discount($coupon, $order);
            $order->coupon = $discount_coupon;
            $order->coupon_id = $coupon->id;
            $order->save();
        }

        foreach ($OrderItems as $item) {
            $orderItem = $this->createItemFromCart($item, true);
            $order->items()->save($orderItem);
            $item->reserve = null; //Для очистки корзины, без затрагивания резерва
        }
        $this->cart->clearOrder(true);

        //Предзаказ
        if ($request['preorder'] == 1 && //В заказе установлена метка для предзаказа.
            !empty($PreOrderItems = $this->cart->getPreOrderItems())) {//и кол-во товаров не пусто
            foreach ($PreOrderItems as $item) {
                $orderItem = $this->createItemFromCart($item, false);
                $orderItem->preorder = true;
                $order->items()->save($orderItem);
            }
            $this->cart->clearPreOrder();
        }

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
        $order = Order::register($default->payment->user_id, Order::PARSER);
        $order->save();
        foreach ($OrderItems as $item) {
            $orderItem = $this->createItemFromCart($item, false);
            $order->items()->save($orderItem);
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
        $order = Order::register($user->id, Order::ONLINE);

        $orderItem = $this->createNewItem($product, 1, true, $user->id);
        $order->items()->save($orderItem);
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
            $user->update(['phone' => $request['phone']]);
            $this->deliveries->user($user->id)
                ->setFullName(new FullName('', $request['name'], ''));
            event(new UserHasCreated($user));
        } else {//2. Пользователь старый.
            $user = User::find((int)$request['user_id']);
        }
        $order = Order::register($user->id, Order::MANUAL); //Создаем пустой заказ
        //Оповещать не нужно event(new OrderHasCreated($order));

        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($staff->id);
        //$order->responsible()->save(OrderResponsible::registerManager($staff->id));
        $order->refresh();
        return $order;
    }

    /**
     * Создаем элемент заказа из элемента корзины
     * @param CartItemInterface $item
     * @param bool $reserve
     * @param int|null $user_id
     * @return OrderItem
     */
    private function createItemFromCart(CartItemInterface $item, bool $reserve, int $user_id = null): OrderItem
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
        $orderItem->preorder = false;
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

    /**
     * Создаем элемент заказа из товара, с резервом или без
     * @param Product $product
     * @param int $quantity
     * @param bool $reserve
     * @param int|null $user_id
     * @return OrderItem
     */
    private function createNewItem(Product $product, int $quantity, bool $reserve = false, int $user_id = null): OrderItem
    {
        if ($reserve) {//Ставим в резерв
            $reserveItem = $this->reserves->toReserve(
                $product,
                $quantity,
                Reserve::TYPE_ORDER,
                $this->minutes,
                $user_id
            );
        }

        $orderItem = new OrderItem();
        $orderItem->preorder = false;
        $orderItem->product_id = $product->id;
        $orderItem->quantity = $quantity;
        $orderItem->base_cost = $product->getLastPrice($user_id);
        $orderItem->sell_cost = $orderItem->base_cost; //Цена продаже равна базовой цене
        $orderItem->options = [];
        $orderItem->reserve_id = ($reserve) ? $reserveItem->id : null;
        return $orderItem;
    }

    //**** ФУНКЦИИ РАБОТЫ С ЗАКАЗОМ МЕНЕДЖЕРОМ
    /**
     * Устанавливаем в ручную доп. скидку на заказ
     * @param Order $order
     * @param int $manual
     * @return Order
     */
    public function update_manual(Order $order, int $manual): Order
    {
        $old = $order->manual;
        $order->manual = $manual;
        $order->save();
        $order->refresh();
        $this->logger->logOrder($order, 'Изменена скидка на заказ', price($old), price($manual));
        return $order;
    }

    /**
     * Добавить в заказ товар, с учетом в наличии
     * @param Order $order
     * @param array $request
     * @return Order
     */
    public function add_item(Order $order, array $request): Order
    {
        $product_id = (int)$request['product_id'];
        $quantity = (int)$request['quantity'];
        $user_id = $order->user->id;
        /** @var Product $product */
        $product = Product::find($product_id);

        //Если товар уже есть, удаляем
        if (!is_null($item = $order->getItem($product, false))) { //по наличию
            $this->reserves->delete($item->reserve);
            $item->delete();
        }
        if (!is_null($item = $order->getItem($product, true))) $item->delete(); //по предзаказу
        $order->refresh();
        $product->refresh();

        $free_count = min($quantity, $product->count_for_sell);
        $preorder_count = max($quantity - $product->count_for_sell, 0);
        if ($free_count > 0) {
            $freeItem = $this->createNewItem($product, $free_count, true, $user_id);
            $freeItem->preorder = false;
            $order->items()->save($freeItem);
        }
        if ($preorder_count > 0) {
            $preItem = $this->createNewItem($product, $preorder_count, false, $user_id);
            $preItem->preorder = true;
            $order->items()->save($preItem);
        }
        //Обновляем резерв для всех товаров из заказа
        foreach ($order->items as $item) {
            if (!$item->preorder) $item->reserve->update(['reserve_at', now()->addMinutes($this->minutes)]);
        }

        $this->recalculation($order);

        $this->logger->logOrder($order, 'Добавлен товар', $product->name, (string)$quantity);

        return $order;
    }

    /**
     * Изменения кол-ва товара, с учетом "в наличии" и перерасчетом всего заказа
     * @param OrderItem $item - изменяемая позиция
     * @param int $quantity - новое значение кол-ва
     * @return Order
     */
    public function update_quantity(OrderItem $item, int $quantity): Order
    {
        $delta = $quantity - $item->quantity;
        if ($delta == 0) return $item->order;

        if (!$item->preorder) {
            $max_free = $item->product->count_for_sell + $item->quantity;
            $quantity = min($max_free, $quantity);
            $this->reserves->UpdateReserve($item->reserve_id, $delta);
        }
        $item->quantity = $quantity;
        $item->save();
        $order = $item->order;

        $this->recalculation($order);
        $order->refresh();
        $this->logger->logOrder($order, 'Изменено кол-во товара', $item->product->name, (string)$quantity);

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
        $item->sell_cost = $sell_cost;
        $item->save();
        $order = $item->order;

        $this->recalculation($order);
        $order->refresh();
        $this->logger->logOrder($order, 'Изменена цена товара', $item->product->name, price($sell_cost));
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

    /**
     * Удаляем позицию в заказе, пересчитываем резерв и скидки по заказу
     * @param OrderItem $item
     * @return Order
     */
    public function delete_item(OrderItem $item): Order
    {
        $order = $item->order;
        $this->logger->logOrder($order, 'Удален товар из заказа', $item->product->name, (string)$item->quantity);
        if (!is_null($item->reserve)) $this->reserves->delete($item->reserve);
        $item->delete();
        $this->recalculation($order);
        return $order;
    }

    /**
     * Добавить в заказ доп.услугу
     * @param Order $order
     * @param array $request
     * @return Order
     */
    public function add_addition(Order $order, array $request): Order
    {
        if ((int)$request['purpose'] == 0) throw new \DomainException('Не выбрана дополнительная услуга!');
        if ((int)$request['amount'] == 0) throw new \DomainException('Стоимость услуги должна быть больше нуля!');
        $orderAddition = OrderAddition::new((int)$request['amount'], (int)$request['purpose'], $request['comment'] ?? '');
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
    public function update_addition(OrderAddition $addition, int $amount): Order
    {
        $addition->amount = $amount;
        $addition->save();
        $this->logger->logOrder($addition->order, 'Изменена цена услуги', $addition->purposeHTML(), price($amount));
        $addition->order->refresh();
        return $addition->order;
    }

    /**
     * Удалить услугу, возвращает Заказ
     * @param OrderAddition $addition
     * @return Order
     */
    public function delete_addition(OrderAddition $addition): Order
    {
        $order = $addition->order;
        $this->logger->logOrder($order, 'Удалена услуга', $addition->purposeHTML(), price($addition->amount));
        $addition->delete();
        return $order;
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

        /** @var OrderItem[] $items */
        $items = $this->calculator->calculate($items);
        foreach ($items as $item) {
            $item->save();
        }

        if (!is_null($discount = $this->calculator->discount($items))) {
            $order->discount_id = $discount->id;
        } else {
            $order->discount_id = null;
        }
        $order->save();
    }

    public function update_comment(Order $order, string $comment): void
    {
        $order->comment = $comment;
        $order->save();
    }

    public function update_item_comment(OrderItem $item, string $comment)
    {
        $item->comment = $comment;
        $item->save();
    }

}
