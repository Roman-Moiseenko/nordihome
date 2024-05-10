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
            $request['weight'] ?? 0,
            $request['volume'] ?? 0,
            isset($request['cargo']),
            isset($request['service']),
        );

        if (isset($request['worker_id'])) $truck->setDriver((int)$request['worker_id']);
        return $truck;
    }

    public function update(array $request, DeliveryTruck $truck): DeliveryTruck
    {
        $truck->update([
            'name' => $request['name'],
            'weight' => $request['weight'] ?? 0,
            'volume' => $request['volume'] ?? 0,
            'cargo' => isset($request['cargo']),
            'service' => isset($request['service'])
        ]);

        if (isset($request['worker_id'])) $truck->setDriver((int)$request['worker_id']);
        $truck->refresh();
        return $truck;
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
