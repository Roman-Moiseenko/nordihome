<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Product\EditPage;

use App\Modules\Catalog\Application\DTOs\Product\EditPage\ViewEquivalentProductData;
use App\Modules\Catalog\Domain\Interfaces\EquivalentProductRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\EquivalentRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;

final readonly class GetEquivalentProductQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private EquivalentRepositoryInterface $equivalentRepository,
        private EquivalentProductRepositoryInterface $equivalentProductRepository,
    ) {
    }

    public function execute(int $id): ViewEquivalentProductData
    {
        $entity = $this->productRepository->getById($id);

        $equivalents = $this->equivalentRepository->getAll();

        $equivalentIds = $this->equivalentProductRepository->getEquivalentIdsByProductId($id);
        $currentId = $equivalentIds[0] ?? null;

        $currentEquivalent = null;
        if ($currentId !== null) {
            $equivalent = $this->equivalentRepository->getById((int) $currentId);

            $productIds = collect(
                $this->equivalentProductRepository
                    ->getProductIdsByEquivalentId((int) $currentId, 500)
                    ->items()
            )
                ->pluck('product_id')
                ->map(fn($v) => (int) $v)
                ->all();

            $products = array_map(
                fn($p) => [
                    'id' => $p->id,
                    'code' => (string) $p->code,
                    'name' => $p->name,
                    'image' => GetPhotoStatic::gallery('catalog.product', $p->id, 'mini'),
                ],
                $this->productRepository->findByIds($productIds),
            );

            $currentEquivalent = [
                'id' => $equivalent->id,
                'name' => $equivalent->name,
                'products' => $products,
            ];
        }

        return ViewEquivalentProductData::create(
            id: $id,
            equivalents: $equivalents,
            currentEquivalentId: $currentId !== null ? (int) $currentId : null,
            currentEquivalent: $currentEquivalent,
            hasModification: $entity->hasModification,
        );
    }
}
