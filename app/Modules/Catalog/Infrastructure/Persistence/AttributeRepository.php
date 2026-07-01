<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCategoryData;
use App\Modules\Catalog\Application\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Entity\Attribute;
use App\Modules\Catalog\Infrastructure\Models\Category;

class AttributeRepository implements AttributeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findForCategory(int $categoryId): array
    {
        $categoryModel = Category::findOrFail($categoryId);

        return [
            'self'   => $this->mapAttributes($categoryModel->prod_attributes()->getModels()),
            'parent' => $this->mapAttributes($categoryModel->parent_attributes()),
        ];
    }

    /**
     * @param Attribute[] $attributes
     * @return AttributeCategoryData[]
     */
    private function mapAttributes(array $attributes): array
    {
        return array_map(function (Attribute $attribute) {
            return new AttributeCategoryData(
                id: $attribute->id,
                name: $attribute->name,
                group: $attribute->group->name,
                filter: $attribute->filter,
                type_text: $attribute->typeText(),
                image: $attribute->getImage(),
            );
        }, $attributes);
    }
}
