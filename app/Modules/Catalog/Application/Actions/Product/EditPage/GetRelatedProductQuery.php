<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewRelatedProductData;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;

final readonly class GetRelatedProductQuery
{
    public function execute(int $id): ViewRelatedProductData
    {
        $product = Product::with('modification')->findOrFail($id);

        $items = $product->related()
            ->get()
            ->map(fn(Product $related) => [
                'id' => $related->id,
                'code' => $related->code ?? '',
                'name' => $related->name,
                'image' => GetPhotoStatic::gallery('catalog.product', $related->id, 'mini'),
            ])
            ->values()
            ->toArray();

        return ViewRelatedProductData::create(
            id: $id,
            products: $items,
            hasModification: $product->modification !== null,
        );
    }
}
