<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shared\Application\Actions\GetImageThumbByRowUseCase;
use Illuminate\Support\Facades\DB;

/**
 * Читает состав модификаций (modifications_products) для сворачивания
 * списка товаров до базовых (is_primary = true) и подготовки
 * массива товаров-вариантов.
 */
class ModificationGroupQueryRepository
{
    private const string ATTRIBUTE_VARIANT_PHOTO_MODEL_TYPE = 'catalog.attribute-variant';

    public function __construct(
        private readonly GetImageThumbByRowUseCase $imageThumbUseCase,
    )
    {
    }

    /**
     * Для переданных ID товаров находит модификации, в которых они участвуют,
     * и возвращает:
     *  - primaryByProduct: [product_id => primary_product_id] — маппинг любого
     *    участника модификации на её базовый товар;
     *  - variantIdsByPrimary: [primary_product_id => int[]] — ID всех товаров
     *    модификации, базовый идёт первым;
     *  - variantNamesByProduct: [product_id => string] — названия вариантов
     *    атрибутов товара (в порядке осей модификации);
     *  - variantImagesByProduct: [product_id => ?string] — ссылка на изображение
     *    варианта атрибута (AttributeVariant) первой оси, если есть.
     *
     * @param int[] $productIds
     * @return array{
     *     primaryByProduct: array<int, int>,
     *     variantIdsByPrimary: array<int, array<int, int>>,
     *     variantNamesByProduct: array<int, string>,
     *     variantImagesByProduct: array<int, ?string>
     * }
     */
    public function resolveGroups(array $productIds): array
    {
        $empty = [
            'primaryByProduct' => [],
            'variantIdsByPrimary' => [],
            'variantNamesByProduct' => [],
            'variantImagesByProduct' => [],
        ];

        if (empty($productIds)) {
            return $empty;
        }

        $modificationIds = DB::table('modifications_products')
            ->whereIn('product_id', $productIds)
            ->distinct()
            ->pluck('modification_id')
            ->map(fn($id) => (int) $id)
            ->all();

        if (empty($modificationIds)) {
            return $empty;
        }

        // Связи товаров с модификациями (только опубликованные товары в продаже).
        $pivots = DB::table('modifications_products')
            ->join('products', 'products.id', '=', 'modifications_products.product_id')
            ->whereIn('modifications_products.modification_id', $modificationIds)
            ->where('products.published', true)
            ->where('products.not_sale', false)
            ->select(
                'modifications_products.id',
                'modifications_products.modification_id',
                'modifications_products.product_id',
                'modifications_products.is_primary',
            )
            ->get();

        // Оси модификаций (modification_attributes), упорядоченные по sort.
        $axesByModification = [];
        $axisRows = DB::table('modification_attributes')
            ->whereIn('modification_id', $modificationIds)
            ->orderBy('sort')
            ->get(['modification_id', 'attribute_id']);

        foreach ($axisRows as $axis) {
            $axesByModification[(int) $axis->modification_id][] = (int) $axis->attribute_id;
        }

        // Названия и ID вариантов для каждого товара модификации.
        $valuesByPivot = [];
        $variantIdsByPivot = [];
        $pivotIds = $pivots->pluck('id')->map(fn($id) => (int) $id)->all();

        if (!empty($pivotIds)) {
            $valueRows = DB::table('modification_product_values')
                ->join('attribute_variants', 'attribute_variants.id', '=', 'modification_product_values.variant_id')
                ->whereIn('modification_product_values.modification_product_id', $pivotIds)
                ->select(
                    'modification_product_values.modification_product_id',
                    'modification_product_values.attribute_id',
                    'modification_product_values.variant_id',
                    'attribute_variants.name',
                )
                ->get();

            foreach ($valueRows as $value) {
                $valuesByPivot[(int) $value->modification_product_id][(int) $value->attribute_id] = $value->name;
                $variantIdsByPivot[(int) $value->modification_product_id][(int) $value->attribute_id] = (int) $value->variant_id;
            }
        }

        $primaryByModification = [];
        $membersByModification = [];
        $pivotByProduct = [];

        foreach ($pivots as $row) {
            $modificationId = (int) $row->modification_id;
            $productId = (int) $row->product_id;

            $membersByModification[$modificationId][] = $productId;
            $pivotByProduct[$productId] = (int) $row->id;

            if ((bool) $row->is_primary) {
                $primaryByModification[$modificationId] = $productId;
            }
        }

        // ID вариантов первой оси для каждого товара (для получения изображения AttributeVariant).
        $firstAxisVariantIds = [];
        foreach ($membersByModification as $modificationId => $members) {
            $axes = $axesByModification[$modificationId] ?? [];
            if ($axes === []) {
                continue;
            }
            $firstAxis = $axes[0];

            foreach ($members as $memberId) {
                $variantId = $variantIdsByPivot[$pivotByProduct[$memberId]][$firstAxis] ?? null;
                if ($variantId !== null) {
                    $firstAxisVariantIds[] = $variantId;
                }
            }
        }

        $variantImagesByProduct = $this->loadVariantImages($firstAxisVariantIds);

        $primaryByProduct = [];
        $variantIdsByPrimary = [];
        $variantNamesByProduct = [];

        foreach ($membersByModification as $modificationId => $members) {
            $primaryId = $primaryByModification[$modificationId] ?? null;
            if ($primaryId === null) {
                continue;
            }

            foreach ($members as $memberId) {
                $primaryByProduct[$memberId] = $primaryId;
            }

            // Все товары модификации, базовый — первым.
            $variantIdsByPrimary[$primaryId] = array_values(
                array_unique(array_merge([$primaryId], array_diff($members, [$primaryId])))
            );

            // Названия вариантов в порядке осей модификации.
            $axes = $axesByModification[$modificationId] ?? [];
            foreach ($members as $memberId) {
                $names = [];
                foreach ($axes as $attributeId) {
                    $name = $valuesByPivot[$pivotByProduct[$memberId]][$attributeId] ?? null;
                    if ($name !== null) {
                        $names[] = $name;
                    }
                }
                $variantNamesByProduct[$memberId] = implode(', ', $names);

                // Изображение варианта первой оси.
                $firstAxisVariantId = $variantIdsByPivot[$pivotByProduct[$memberId]][$axes[0] ?? ''] ?? null;
                $variantImagesByProduct[$memberId] = $firstAxisVariantId !== null
                    ? ($variantImagesByProduct[$firstAxisVariantId] ?? null)
                    : null;
            }
        }

        return [
            'primaryByProduct' => $primaryByProduct,
            'variantIdsByPrimary' => $variantIdsByPrimary,
            'variantNamesByProduct' => $variantNamesByProduct,
            'variantImagesByProduct' => $variantImagesByProduct,
        ];
    }

    /**
     * Возвращает URL изображений вариантов атрибутов по их ID.
     *
     * @param int[] $variantIds
     * @return array<int, string> variant_id => url
     */
    private function loadVariantImages(array $variantIds): array
    {
        $variantIds = array_values(array_unique($variantIds));
        if (empty($variantIds)) {
            return [];
        }

        $photos = DB::table('photos')
            ->whereIn('imageable_id', $variantIds)
            ->where('model_type', self::ATTRIBUTE_VARIANT_PHOTO_MODEL_TYPE)
            ->where('type', 'image')
            ->select('imageable_id', 'id', 'file', 'model_type')
            ->get();

        $result = [];
        foreach ($photos as $photo) {
            $row = new \stdClass();
            $row->id = (int) $photo->imageable_id;
            $row->photo_id = (int) $photo->id;
            $row->photo_file = $photo->file;
            $row->model_type = $photo->model_type;

            $result[(int) $photo->imageable_id] = $this->imageThumbUseCase->execute($row);
        }

        return $result;
    }
}
