<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Delivery\Entity\DeliveryTruck;
use Illuminate\Http\Request;

class TruckService
{

    public function register(Request $request): DeliveryTruck
    {
        return DeliveryTruck::register(
            $request->string('name')->trim()->value(),
            $request->float('weight'),
            $request->float('volume'),
        );
    }

    public function setInfo(Request $request, DeliveryTruck $truck): void
    {
        $truck->name = $request->string('name')->trim()->value();
        $truck->weight = $request->float('weight');
        $truck->volume = $request->float('volume');
        $truck->save();

    }

    public function delete(DeliveryTruck $truck): void
    {
        if ($truck->isActive()) throw new \DomainException('Транспорт используется, удалить нельзя');
        $truck->delete();
    }
}
