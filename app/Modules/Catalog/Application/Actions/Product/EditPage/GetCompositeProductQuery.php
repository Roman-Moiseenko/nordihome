<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewCompositeProductData;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;

final readonly class GetCompositeProductQuery
{
    public function execute(int $id): ViewCompositeProductData
    {
        $product = Product::with('modification')->findOrFail($id);

        $items = $product->composites()
            ->get()
            ->map(fn(Product $child) => [
                'id' => $child->id,
                'code' => $child->code ?? '',
                'name' => $child->name,
                'image' => GetPhotoStatic::gallery('catalog.product', $child->id, 'mini'),
                'quantity' => (int) ($child->pivot->quantity ?? 1),
            ])
            ->values()
            ->toArray();

        return ViewCompositeProductData::create(
            id: $id,
            products: $items,
            hasModification: $product->modification !== null,
        );
    }
}
