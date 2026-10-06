<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Attribute\AttributeCategoryData;
use App\Modules\Catalog\Application\DTOs\Attribute\FilterAttributeIndexData;
use App\Modules\Catalog\Domain\Entities\AttributeEntity;
use App\Modules\Catalog\Domain\Entities\AttributeVariantEntity;
use App\Modules\Catalog\Domain\Interfaces\AttributeRepositoryInterface;
use App\Modules\Catalog\Domain\ValueObjects\AttributeType;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Infrastructure\Models\AttributeVariant;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

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

    public function getById(int $id): AttributeEntity
    {
        return $this->hydrate(Attribute::with('variants')->findOrFail($id));
    }

    public function getFilteredPaginated(FilterAttributeIndexData &$filter): LengthAwarePaginator
    {
        $query = Attribute::with(['variants'])
            ->orderBy('name');

        $filter->count = 0;

        if (!is_null($filter->name) && trim($filter->name) !== '') {
            $name = trim($filter->name);
            $query->whereRaw('LOWER(name) like LOWER(?)', ["%{$name}%"]);
            $filter->count++;
        }

        if (!is_null($filter->groupId) && $filter->groupId > 0) {
            $query->where('group_id', $filter->groupId);
            $filter->count++;
        }

        if (!is_null($filter->categoryId) && $filter->categoryId > 0) {
            $query->whereHas('categories', function ($q) use ($filter) {
                $q->where('id', $filter->categoryId);
            });
            $filter->count++;
        }

        if (!is_null($filter->filter)) {
            $query->where('filter', $filter->filter);
            $filter->count++;
        }

        return $query->paginate($filter->perPage)
            ->withQueryString()
            ->through(fn(Attribute $model) => $this->hydrate($model));
    }

    public function save(AttributeEntity $attribute): AttributeEntity
    {
        $model = $attribute->id
            ? Attribute::findOrFail($attribute->id)
            : new Attribute();

        $model->name = $attribute->name;
        $model->type = (string) $attribute->type;
        $model->group_id = $attribute->groupId;
        $model->multiple = $attribute->multiple;
        $model->filter = $attribute->filter;
        $model->show_in = $attribute->showIn;
        $model->sameAs = $attribute->sameAs ?? '';

        $model->save();

        $this->syncVariants($model, $attribute->variants);

        return $this->hydrate($model->fresh()->load('variants'));
    }

    public function delete(int $id): void
    {
        $model = Attribute::findOrFail($id);

        AttributeVariant::where('attribute_id', $model->id)->delete();
        $model->delete();
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
                //TODO Заменить на UseCase
                image: GetPhotoStatic::get('catalog.attribute', $attribute->id),
            );
        }, $attributes);
    }

    private function hydrate(Attribute $model): AttributeEntity
    {
        $entity = new AttributeEntity(
            name: $model->name,
            type: new AttributeType($model->type),
            groupId: $model->group_id,
        );

        $entity->id = $model->id;
        $entity->multiple = (bool) $model->multiple;
        $entity->filter = (bool) $model->filter;
        $entity->showIn = (bool) $model->show_in;
        $entity->sameAs = $model->sameAs ?: null;

        if ($model->relationLoaded('variants')) {
            $entity->variants = $model->variants
                ->map(fn(AttributeVariant $variant) => $this->hydrateVariant($variant))
                ->all();
        }

        return $entity;
    }

    private function hydrateVariant(AttributeVariant $model): AttributeVariantEntity
    {
        $entity = new AttributeVariantEntity(
            name: $model->name,
            slug: $model->slug,
        );

        $entity->id = $model->id;
        $entity->attributeId = $model->attribute_id;

        return $entity;
    }

    /**
     * Синхронизация вариантов атрибута: обновление существующих,
     * добавление новых и удаление отсутствующих в сущности.
     *
     * @param AttributeVariantEntity[] $variants
     */
    private function syncVariants(Attribute $model, array $variants): void
    {
        $existingIds = AttributeVariant::where('attribute_id', $model->id)
            ->pluck('id')
            ->all();

        $keepIds = [];

        foreach ($variants as $variant) {
            if ($variant->id !== null) {
                $variantModel = AttributeVariant::find($variant->id);
                if ($variantModel === null) {
                    continue;
                }

                $variantModel->name = $variant->name;
                $variantModel->slug = $variant->slug ?: Str::slug($variant->name);
                $variantModel->save();

                $keepIds[] = $variantModel->id;
            } else {
                $variantModel = AttributeVariant::register($variant->name);
                $model->variants()->save($variantModel);

                $keepIds[] = $variantModel->id;
            }
        }

        $removeIds = array_values(array_diff($existingIds, $keepIds));
        if (!empty($removeIds)) {
            AttributeVariant::whereIn('id', $removeIds)->delete();
        }
    }
}
