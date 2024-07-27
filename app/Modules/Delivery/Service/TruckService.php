<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Delivery\Entity\DeliveryTruck;

class TruckService
{

    public function register(array $request): DeliveryTruck
    {
        $truck = DeliveryTruck::register(
            $request['name'],
            (float)$request['weight'] ?? 0,
            (float)$request['volume'] ?? 0,
            isset($request['cargo']),
            isset($request['service']),
        );

        if (isset($request['worker_id'])) $truck->setDriver((int)$request['worker_id']);
        return $truck;
    }

    public function update(array $request, DeliveryTruck $truck)
    {
        $truck->update([
            'name' => $request['name'],
            'weight' => $request['weight'] ?? 0,
            'volume' => $request['volume'] ?? 0,
            'cargo' => isset($request['cargo']),
            'service' => isset($request['service'])
        ]);

        if (isset($request['worker_id'])) $truck->setDriver((int)$request['worker_id']);
    }

    public function draft(DeliveryTruck $truck)
    {
        //Проверка, есть ли активные доставки ??
        $truck->active = false;
        $truck->save();
    }

    public function activated(DeliveryTruck $truck)
    {
        $truck->active = true;
        $truck->save();

    }

    public function delete(DeliveryTruck $truck)
    {

    }
}
