<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewBonusProductData;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;

final readonly class GetBonusProductQuery
{
    public function execute(int $id): ViewBonusProductData
    {
        $product = Product::with('modification')->findOrFail($id);

        $items = $product->bonus()
            ->get()
            ->map(fn(Product $bonus) => [
                'id' => $bonus->id,
                'code' => $bonus->code ?? '',
                'name' => $bonus->name,
                'image' => GetPhotoStatic::gallery('catalog.product', $bonus->id, 'mini'),
                'price' => $bonus->getPriceRetail() ?? 0,
                'discount' => (int) ($bonus->pivot->discount ?? 0),
            ])
            ->values()
            ->toArray();

        return ViewBonusProductData::create(
            id: $id,
            products: $items,
            hasModification: $product->modification !== null,
        );
    }
}
