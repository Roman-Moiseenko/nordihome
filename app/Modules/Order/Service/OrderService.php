<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Entity\GeoAddress;
use App\Modules\Delivery\Entity\UserDelivery;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Order\Entity\UserPayment;
use App\Modules\Shop\Cart\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class OrderService
{

    private PaymentService $payments;
    private DeliveryService $deliveries;
    private Cart $cart;

    public function __construct(PaymentService $payments, DeliveryService $deliveries, Cart $cart)
    {
        $this->payments = $payments;
        $this->deliveries = $deliveries;
        $this->cart = $cart;
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

    public function checkorder(Request $request): array
    {

        $enabled = true;
        $error = '';
        $delivery_cost = 0;
        $data = $request['data'];

        //TODO Глобально:
        //1. Проверка на допустимость

        $default = $this->default_user_data();



        if (isset($data['payment'])) $default->payment->setPayment($data['payment']);
        if (isset($data['inn'])) {
            //Работа с ИНН
            //Проверяем изменился или новые данные, если да, загружаем по API
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
            //Считаем стоимость доставки

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
        $delivery_cost = $this->deliveries->calculate($default->delivery->user_id, $this->cart->getItems());

        //5. Формирование массива на выдачу:
        //5.1. Стоимость доставки
        //5.2. Скидка на заказ от купона
        //5.3. Итоговая стоимость
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
            ],
        ];



        return $result;
    }
}
