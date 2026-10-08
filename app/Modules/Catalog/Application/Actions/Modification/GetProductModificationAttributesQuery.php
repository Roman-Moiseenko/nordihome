<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Modification;

use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Shared\Application\DTOs\ListNameData;

/**
 * Данные для диалога создания модификации из товара на странице редактирования:
 *  - название товара (для поля name);
 *  - атрибуты-варианты товара (одиночные, без множественного выбора).
 */
final readonly class GetProductModificationAttributesQuery
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private AttributeRepositoryInterface $attributeRepository,
    ) {
    }

    /**
     * @return array{name: string, attributes: ListNameData[]}
     */
    public function execute(int $productId): array
    {
        $product = $this->productRepository->getById($productId);

        return [
            'name' => $product->name,
            'attributes' => array_map(
                fn(AttributeEntity $entity) => new ListNameData(
                    id: $entity->id,
                    name: $entity->name,
                ),
                $this->attributeRepository->getModificationAttributesForProducts([$productId]),
            ),
        ];
    }
}
