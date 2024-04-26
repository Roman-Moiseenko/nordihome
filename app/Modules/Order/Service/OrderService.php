<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Entity\FullName;
use App\Entity\GeoAddress;
use App\Events\OrderHasCreated;
use App\Events\UserHasCreated;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\LoggerService;
use App\Modules\Delivery\Entity\DeliveryOrder;
use App\Modules\Delivery\Entity\UserDelivery;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Discount\Service\CouponService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\UserPayment;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\Calculate\CalculatorOrder;
use App\Modules\Shop\Cart\Cart;
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
    private ParserCart $parserCart;
    private CalculatorOrder $calculator;
    private LoggerService $logger;
    private MovementService $movementService;
    private OrderReserveService $reserveService;



    public function __construct(
        PaymentService      $payments,
        DeliveryService     $deliveries,
        Cart                $cart,
        ParserCart          $parserCart,
        ShopRepository      $repository,
        CouponService       $coupons,
        //ReserveService      $reserves,
        CalculatorOrder     $calculator,
        LoggerService       $logger,
        MovementService     $movementService,
        OrderReserveService $reserveService,
    )
    {
        $this->payments = $payments;
        $this->deliveries = $deliveries;
        $this->cart = $cart;
        $this->coupon = (new Options())->shop->coupon;
        $this->repository = $repository;
        $this->coupons = $coupons;
       // $this->minutes = (new Options())->shop->reserve_order;
       // $this->reserves = $reserves;
        $this->parserCart = $parserCart;
        $this->calculator = $calculator;
        $this->logger = $logger;
        $this->movementService = $movementService;
        $this->reserveService = $reserveService;
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

        //$OrderItems = $this->cart->getOrderItems();
        $order = Order::register($default->payment->user_id, Order::ONLINE);
        if ($request->has('code')) {
            $coupon = $this->repository->getCoupon($request->get('code'));
            $discount_coupon = $this->coupons->discount($coupon, $order);
            $order->coupon = $discount_coupon;
            $order->coupon_id = $coupon->id;
            $order->save();
        }

        $items = ($request['preorder'] == 1) ? $this->cart->getItems() : $this->cart->getOrderItems();
        foreach ($items as $item) {
            if ($item->check) $this->add_product($order, $item->product->id, $item->quantity);
            //$orderItem = $this->createItemFromCart($item);
            //$order->items()->save($orderItem);
        }
        $this->cart->clearOrder();
        if ($request['preorder'] == 1) $this->cart->clearPreOrder();

        $order->refresh();
        $this->recalculation($order);

        //Предзаказ
        /*
        if ($request['preorder'] == 1 && //В заказе установлена метка для предзаказа.
            !empty($PreOrderItems = $this->cart->getPreOrderItems())) {//и кол-во товаров не пусто
            foreach ($PreOrderItems as $item) {
                $orderItem = $this->createItemFromCart($item);
                $orderItem->preorder = true;
                $order->items()->save($orderItem);
            }
            $this->cart->clearPreOrder();
        }
*/
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
            $orderItemPre = OrderItem::new($item->product, $item->quantity, true, $order->user_id);
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
            $user->update(['phone' => $request['phone']]);
            $this->deliveries->user($user->id)
                ->setFullName(new FullName('', $request['name'], ''));
            event(new UserHasCreated($user));
        } else {//2. Пользователь старый.
            $user = User::find((int)$request['user_id']);
        }
        $order = Order::register($user->id, Order::MANUAL); //Создаем пустой заказ

        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $order->setStatus(OrderStatus::SET_MANAGER);
        $order->setManager($staff->id);
        $order->refresh();
        return $order;
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
        //TODO Пересчет скидки для товара ! Добавить параметр float $percent
        $old = $order->manual;
        $order->manual = $manual;
        $order->save();
        $order->refresh();
        $this->logger->logOrder($order, 'Изменена скидка на заказ', price($old), price($manual));
        return $order;
    }


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

        $orderItem = OrderItem::new($product, $quantity, false, $order->user_id);
        $order->items()->save($orderItem);
        $this->reserveService->toReserve($orderItem, $quantity);

        if ($quantity_preorder > 0) {
            $orderItemPre = OrderItem::new($product, $quantity_preorder, true, $order->user_id);
            $order->items()->save($orderItemPre);
        }
        $order->refresh();
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
        //TODO Если есть купон, обсчитываем
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

    public function movement(Order $order, int $storage_out, int $storage_in)
    {
        $movement = $this->movementService->create([
            //'number' => 'Заказ ' . $order->htmlNum(),
            'storage_out' => $storage_out,
            'storage_in' => $storage_in,
        ]);
        $order->movements()->attach($movement->id);

        foreach ($order->items as $item) {
            if (!is_null($reserve = $item->getReserveByStorage($storage_out))) {
                if ($reserve->quantity > 0) {
                    $movement->addProduct($item->product, $reserve->quantity, $item->id);
                }
            }

            /*
            if ($item->getRemains() != 0 && $item->preorder == false)
                $movement->addProduct($item->product, $item->getRemains());*/
        }
        return $movement;
    }

}
