<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Repository;

use App\Modules\Delivery\Entity\DeliveryTruck;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class TruckRepository
{

    public function getIndex(Request $request): Arrayable
    {
        return DeliveryTruck::orderBy('name')->get()->map(fn(DeliveryTruck $truck) => $truck->toArray());
    }
}
