<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Persistence;

use App\Modules\Catalog\Application\DTOs\Modification\ModificationAttributeViewData;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationIndexData;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationProductViewData;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationVariantViewData;
use App\Modules\Catalog\Application\DTOs\Modification\ModificationViewData;
use App\Modules\Catalog\Domain\Entities\ModificationEntity;
use App\Modules\Catalog\Domain\Entities\ModificationProductEntity;
use App\Modules\Catalog\Domain\Interfaces\ModificationRepositoryInterface;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Infrastructure\Models\AttributeVariant;
use App\Modules\Catalog\Infrastructure\Models\Modification;
use App\Modules\Catalog\Infrastructure\Models\ModificationAttribute;
use App\Modules\Catalog\Infrastructure\Models\ModificationProduct;
use App\Modules\Catalog\Infrastructure\Models\ModificationProductValue;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shared\Application\Actions\GetPhotoStatic;
use Illuminate\Pagination\LengthAwarePaginator;

class ModificationRepository implements ModificationRepositoryInterface
{
    public function getById(int $id): ModificationEntity
    {
        return $this->hydrate(Modification::findOrFail($id));
    }

    public function getViewData(int $id): ModificationViewData
    {
        $model = Modification::with(['attributes.variants', 'products'])
            ->findOrFail($id);

        $attributes = $model->attributes
            ->map(fn(Attribute $attribute) => new ModificationAttributeViewData(
                id: $attribute->id,
                name: $attribute->name,
                variants: $attribute->variants
                    ->map(fn(AttributeVariant $variant) => new ModificationVariantViewData(
                        id: $variant->id,
                        name: $variant->name,
                    ))
                    ->all(),
            ))
            ->all();

        $attributeIds = array_map(
            fn(ModificationAttributeViewData $attribute) => $attribute->id,
            $attributes,
        );

        $pivotIds = $model->products
            ->map(fn(Product $product) => (int) $product->pivot->id)
            ->all();

        $valueRows = ModificationProductValue::whereIn('modification_product_id', $pivotIds)->get();

        $variantNames = AttributeVariant::whereIn('id', $valueRows->pluck('variant_id')->unique()->all())
            ->pluck('name', 'id')
            ->all();

        $usedVariantIds = $valueRows->pluck('variant_id')
            ->map(fn($variantId) => (int) $variantId)
            ->unique()
            ->values()
            ->all();

        $products = $model->products->map(
            function (Product $product) use ($valueRows, $variantNames, $attributeIds) {
                $pivotId = (int) $product->pivot->id;

                $rowValues = $valueRows
                    ->where('modification_product_id', $pivotId)
                    ->mapWithKeys(
                        fn(ModificationProductValue $value) => [
                            (int) $value->attribute_id => (int) $value->variant_id,
                        ],
                    )
                    ->all();

                $values = [];
                foreach ($attributeIds as $attributeId) {
                    if (isset($rowValues[$attributeId]) && isset($variantNames[$rowValues[$attributeId]])) {
                        $values[] = $variantNames[$rowValues[$attributeId]];
                    }
                }

                return new ModificationProductViewData(
                    id: $pivotId,
                    productId: (int) $product->id,
                    name: $product->name,
                    code: $product->code,
                    image: GetPhotoStatic::gallery('catalog.product', $product->id, 'mini'),
                    values: $values,
                    isPrimary: (bool) $product->pivot->is_primary,
                );
            },
        )->all();

        return new ModificationViewData(
            id: $model->id,
            name: $model->name,
            attributes: $attributes,
            products: $products,
            usedVariantIds: $usedVariantIds,
        );
    }

    public function getUsedProductIds(): array
    {
        return ModificationProduct::query()
            ->distinct()
            ->pluck('product_id')
            ->map(fn($id) => (int) $id)
            ->all();
    }

    public function save(ModificationEntity $modification): ModificationEntity
    {
        $model = $modification->id
            ? Modification::findOrFail($modification->id)
            : new Modification();

        $model->name = $modification->name;
        $model->save();

        $this->syncAttributes($model->id, $modification->attributes);
        $this->syncProducts($model->id, $modification->products);

        return $this->hydrate($model->fresh());
    }

    public function delete(int $id): void
    {
        $model = Modification::findOrFail($id);

        ModificationProductValue::whereIn(
            'modification_product_id',
            ModificationProduct::where('modification_id', $id)->pluck('id'),
        )->delete();

        ModificationProduct::where('modification_id', $id)->delete();
        ModificationAttribute::where('modification_id', $id)->delete();

        $model->delete();
    }

    public function findAll(int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        $paginator = Modification::query()
            ->orderByDesc('id')
            ->withCount('products')
            ->with('attributes:id,name')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        $modificationIds = $paginator->getCollection()->pluck('id')->all();

        $primaryMap = ModificationProduct::query()
            ->whereIn('modification_id', $modificationIds)
            ->where('is_primary', true)
            ->pluck('product_id', 'modification_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $paginator->setCollection(
            $paginator->getCollection()->map(
                fn(Modification $model) => $this->toIndexData($model, $primaryMap)
            )
        );

        return $paginator;
    }

    /**
     * @param array<int, int> $primaryMap modification_id => product_id
     */
    private function toIndexData(Modification $model, array $primaryMap): ModificationIndexData
    {
        return new ModificationIndexData(
            id: $model->id,
            name: $model->name,
            quantity: (int) $model->products_count,
            primaryProductId: $primaryMap[$model->id] ?? null,
            nameAttributes: $model->attributes->pluck('name')->all(),
        );
    }

    /**
     * Синхронизация осей модификации (modification_attributes).
     *
     * @param int[] $attributes
     */
    private function syncAttributes(int $modificationId, array $attributes): void
    {
        ModificationAttribute::where('modification_id', $modificationId)->delete();

        foreach ($attributes as $sort => $attributeId) {
            $row = new ModificationAttribute();
            $row->modification_id = $modificationId;
            $row->attribute_id = (int) $attributeId;
            $row->sort = (int) $sort;
            $row->save();
        }
    }

    /**
     * Синхронизация товаров (modifications_products) и их значений.
     *
     * @param array<int, ModificationProductEntity> $products
     */
    private function syncProducts(int $modificationId, array $products): void
    {
        $existing = ModificationProduct::where('modification_id', $modificationId)
            ->get()
            ->keyBy('product_id');

        $keepIds = [];

        foreach ($products as $product) {
            $row = $existing->get($product->productId);

            if ($row === null) {
                $row = new ModificationProduct();
                $row->modification_id = $modificationId;
                $row->product_id = $product->productId;
            }

            $row->is_primary = $product->isPrimary;
            $row->save();

            $keepIds[] = $row->id;

            $this->syncValues($row->id, $product->values);
        }

        ModificationProduct::where('modification_id', $modificationId)
            ->whereNotIn('id', $keepIds)
            ->delete();
    }

    /**
     * Синхронизация значений товара (modification_product_values).
     *
     * @param array<int, int> $values attribute_id => variant_id
     */
    private function syncValues(int $modificationProductId, array $values): void
    {
        ModificationProductValue::where('modification_product_id', $modificationProductId)->delete();

        foreach ($values as $attributeId => $variantId) {
            $row = new ModificationProductValue();
            $row->modification_product_id = $modificationProductId;
            $row->attribute_id = (int) $attributeId;
            $row->variant_id = (int) $variantId;
            $row->save();
        }
    }

    private function hydrate(Modification $model): ModificationEntity
    {
        $attributeIds = ModificationAttribute::where('modification_id', $model->id)
            ->orderBy('sort')
            ->pluck('attribute_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $products = [];

        $rows = ModificationProduct::where('modification_id', $model->id)
            ->with('values')
            ->get();

        foreach ($rows as $row) {
            $values = [];
            foreach ($row->values as $value) {
                $values[(int) $value->attribute_id] = (int) $value->variant_id;
            }

            $products[] = new ModificationProductEntity(
                productId: (int) $row->product_id,
                values: $values,
                isPrimary: (bool) $row->is_primary,
            );
        }

        $entity = new ModificationEntity(
            name: $model->name,
            attributes: $attributeIds,
            products: $products,
        );

        $entity->id = $model->id;

        return $entity;
    }
}
