<?php
declare(strict_types=1);

namespace App\Modules\Discount\Repository;

use App\Modules\Discount\Entity\Discount;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class DiscountRepository
{
    public function getIndex(Request $request): Arrayable
    {
        return Discount::orderBy('name')->get()->map(fn(Discount $discount) => $this->DiscountWithToArray($discount));
    }

    public function DiscountWithToArray(Discount $discount): array
    {
        return array_merge($discount->toArray(), [
            'caption' => $discount->caption(),
            'type' => $discount->nameType(),
        ]);
    }
}
