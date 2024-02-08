<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Events\OrderHasCreated;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Delivery\Entity\DeliveryOrder;
use App\Modules\Delivery\Entity\Local\Delivery;
use App\Modules\Delivery\Entity\Local\Tariff;
use App\Modules\Delivery\Entity\Transport\DeliveryData;
use App\Modules\Delivery\Entity\UserDelivery;
use App\Modules\Delivery\Helpers\DeliveryHelper;
use App\Modules\Shop\CartItemInterface;

class DeliveryService
{

    public function user(int $user_id): UserDelivery
    {
        if ($user = UserDelivery::where('user_id', $user_id)->first()) return $user;
        return UserDelivery::register($user_id);
    }

    public function get(int $user_id): array
    {
        //Получаем список всех доставок
        //Получаем default для клиента

        //Если default нет


        return [];
    }

    public function storages()
    {
        $storages = Storage::where('point_of_delivery', true)->get();
        return $storages;
    }

    public function companies(): array
    {
        $delivery = DeliveryHelper::deliveries();
        return $delivery;
    }

    /**
     * @param CartItemInterface[] $items
     */
    public function calculate(int $user_id, array $items): DeliveryData
    {
        $user_delivery = $this->user($user_id);

        if ($user_delivery->isRegion() && !empty($user_delivery->region->address) && !empty($user_delivery->company)) {
            return DeliveryHelper::calculate($user_delivery->company, $items, []);
        }
        if ($user_delivery->isLocal() && !empty($user_delivery->local->address)) {
            //TODO Таблица с местным расчетом стоимости доставки + Вес
            $distance = $this->distance();
            $cost = Tariff::orderBy('distance')->where('distance', '>', $distance)->first();
            return new DeliveryData($cost->tariff, 3);
        }
        return new DeliveryData(0, 0);
    }

    //TODO расчет расстояния
    private function distance()
    {
        //Получаем базовые координаты центра из настроек


        //Получаем координаты точки доставки

        return 22; //расстояние в км
    }

    public function create(int $order_id, int $type_delivery, string $address)
    {
        $delivery = DeliveryOrder::register($order_id, $type_delivery, $address);
        //TODO Оповещение о новых доставках
        event($delivery);
    }

    //Слушатель события создания Заказа
    public function handle(OrderHasCreated $event): void
    {
        $order = $event->order;
        $delivery = DeliveryOrder::register($order->id, $this->user($order->user_id)->type, $this->user($order->user_id)->getAddressDelivery());
        event($delivery);
    }
}
