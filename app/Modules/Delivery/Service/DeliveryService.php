<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Delivery\Helpers\DeliveryHelper;

class DeliveryService
{


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
}
