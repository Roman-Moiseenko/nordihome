<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Entity\GeoAddress;
use App\Modules\Admin\Entity\Options;
use App\Modules\Delivery\Entity\UserDelivery;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Discount\Service\CouponService;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderStatus;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Order\Entity\UserPayment;
use App\Modules\Shop\Cart\Cart;
use App\Modules\Shop\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function __construct(
        PaymentService  $payments,
        DeliveryService $deliveries,
        Cart            $cart,
        ShopRepository  $repository,
        CouponService   $coupons,
        ReserveService  $reserves
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
    }

    public function default_user_data(): stdClass
    {
        $user_id = Auth::guard('user')->user()->id;
        $result = new stdClass();
        /** @var UserPayment payment */
        $result->payment = $this->payments->user($user_id);
        /** @var UserDelivery delivery */
        $result->delivery = $this->deliveries->user($user_id);
        return $result;
    }

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
        //Считаем стоимость доставки
        $delivery_cost = $this->deliveries->calculate($default->delivery->user_id, $this->cart->getItems());

        //5. Формирование массива на выдачу:

        //5.2. Скидка на заказ от купона ???

        //6. Оформить/Оплатить доступна или нет

        //7. Сообщения об ошибках - неверное ИНН, Негабаритный груз для доставки (название продукта)
        if ($default->delivery->isStorage() && empty($default->delivery->storage)) $enabled = false;
        if ($default->delivery->isLocal() && empty($default->delivery->local->address)) $enabled = false;
        if (
            $default->delivery->isRegion() &&
            (empty($default->delivery->region->address) || empty($default->delivery->company))
        ) $enabled = false;

        $result = [
            'payment' => [
                'is_invoice' => $default->payment->isInvoice(),
                'invoice' => $default->payment->invoice(),
            ],
            'delivery' => [
                'delivery_local' => $default->delivery->local->address,
                'delivery_address' => $default->delivery->region->address,
                'company' => $default->delivery->company,
                'storage' => $default->delivery->storage,
            ],
            'amount' => [
                'delivery' => $delivery_cost,
                'caption' => $default->payment->online() ? 'Оплатить' : 'Оформить',
                'enabled' => $enabled,
                'error' => $error,
                //'amount' =>
            ],
        ];


        return $result;
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

    public function create(Request $request)
    {
        $default = $this->default_user_data();
        $OrderItems = $this->cart->getOrderItems();
        $cart_order = $this->cart->info->order;

        //Создать Order
        $order = Order::register($default->payment->user_id, Order::ONLINE, false);

        $discount_coupon = 0;
        if ($request->has('code')) {
            $coupon = $this->repository->getCoupon($request->get('code'));
            //TODO Сервис  по скидкам и купонам
            $discount_coupon = $this->coupons->discount($coupon, $OrderItems);
        }

        $order->setFinance(
            $cart_order->amount,
            ($cart_order->amount - $cart_order->discount),
            $discount_coupon,
            (empty($coupon) || $discount_coupon == 0) ? null : $coupon->id);

        foreach ($OrderItems as $item) {
            if (is_null($item->reserve)) {
                //Добавить товар в резерв
                $reserve = $this->reserves->toReserve(
                    $item->product,
                    $item->quantity,
                    Reserve::TYPE_ORDER,
                    $this->minutes
                );
                $reserve_id = $reserve->id;
            } else {
                $item->reserve->update([
                    'reserve_at' => now()->addMinutes($this->minutes),
                    'type' => Reserve::TYPE_ORDER,
                ]);
                $reserve_id = $item->reserve->id;
                $item->reserve = null; //Для очистки корзины, без затрагивания резерва
            }

            $order->items()->create([
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'base_cost' => $item->base_cost,
                'sell_cost' => ($item->discount_cost == 0) ? $item->base_cost : $item->discount_cost,
                'discount_id' => $item->discount_id ?? null,
                'discount_type' => $item->discount_type ?? '',
                'options' => $item->options,
                'reserve_id' => $reserve_id
            ]);
        }
        $this->cart->clearOrder(true);
/// 2) ожидание оплаты
        $order->setStatus(OrderStatus::AWAITING);

        //Доставка
        $this->deliveries->create($order->id, $default->delivery->type, $default->delivery->getAddressDelivery());

        //Предзаказ
        if ($request['preorder'] == 1) {//В заказе установлена метка для предзаказа.
            $PreOrderItems = $this->cart->getPreOrderItems();
            $cart_preorder = $this->cart->info->pre_order;

            $preorder = Order::register($default->payment->user_id, Order::ONLINE, true);
            $preorder->setFinance($cart_preorder->amount, $cart_preorder->amount, 0, null);

            foreach ($PreOrderItems as $item) {
                $preorder->items()->create([
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'base_cost' => $item->base_cost,
                    'sell_cost' => $item->base_cost,
                    'options' => $item->options
                ]);
            }
            $this->cart->clearPreOrder();
            $preorder->setStatus(OrderStatus::PREORDER_SERVICE);
        }

        //TODO Очистка корзины проверить



        //Создать платеж или
        /// внести платеж в Заказ
        ///
        //Создать заявку на доставку
        ///внести доставку в Заказ
        ///
        //Отправить документы клиенту:
        /// 1. Счет на ООО
        /// 2. Данные для перевода денег
        /// 3. Информацию, о счете ()
        /// 4. Время резерва, если не онлайн
        ///
        return $order;
    }

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

    public function create_parser(Request $request)
    {
        //TODO Формируем заказ из Парсере

    }
}
