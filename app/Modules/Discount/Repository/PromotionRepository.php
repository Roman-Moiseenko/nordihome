<?php
declare(strict_types=1);

namespace App\Modules\Discount\Repository;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class PromotionRepository
{

    private function PromotionToArray(Promotion $promotion): array
    {
        return array_merge($promotion->toArray(), [
            'status' => $promotion->status,
            'image' => $promotion->getImage(),
            'is_finished' => $promotion->status == 'finished',
            'quantity' => $promotion->products()->count(),
        ]);
    }

    public function PromotionWithToArray(Promotion $promotion): array
    {
        return array_merge($this->PromotionToArray($promotion), [
            'products' => $promotion->products()->get()->map(function (Product $product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'image' => $product->miniImage(),
                    'price' => $product->getPriceRetail(),
                    'discount' => $product->pivot->price
                ];
            })
        ]);
    }

}
