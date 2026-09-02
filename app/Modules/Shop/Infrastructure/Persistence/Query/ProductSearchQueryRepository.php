<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use Illuminate\Support\Facades\DB;

class ProductSearchQueryRepository
{
    /**
     * Поля таблицы products, по которым производится поиск.
     */
    private const array PRODUCT_SEARCH_FIELDS = [
        'name',
        'code',
        'code_search',
        'short',
        'care',
//        'description',
        'model',
    ];

    /**
     * Возвращает массив ID товаров, соответствующих поисковому запросу.
     *
     * Поисковый запрос разбивается на отдельные слова.
     * Для каждого слова поиск осуществляется по полям таблицы products,
     * названию бренда и названиям вариантов атрибутов.
     * Между словами действует логика AND — товар должен соответствовать каждому слову.
     * Регистр букв игнорируется.
     *
     * @param string $search Поисковый запрос (может состоять из нескольких слов)
     * @return int[]
     */
    public function getProductIdsBySearch(string $search): array
    {
        $words = $this->splitSearchIntoWords($search);

        if (empty($words)) {
            return [];
        }

        $result = null;

        foreach ($words as $word) {
            $ids = $this->findProductIdsByWord($word);

            if ($result === null) {
                $result = $ids;
            } else {
                $result = array_intersect($result, $ids);
            }

            // Если на каком-то этапе пересечение дало пустой результат — дальше искать нет смысла
            if (empty($result)) {
                return [];
            }
        }

        return array_values($result);
    }

    /**
     * Разбивает поисковую строку на отдельные слова.
     *
     * @return string[]
     */
    private function splitSearchIntoWords(string $search): array
    {
        $words = explode(' ', trim($search));

        return array_values(
            array_filter($words, static fn(string $word): bool => $word !== '')
        );
    }

    /**
     * Находит ID товаров, соответствующих одному слову.
     *
     * Поиск ведётся по:
     *  - полям таблицы products (name, code, code_search, short, care, description, model)
     *  - названию бренда (brands.name)
     *  - названиям вариантов атрибутов (attribute_variants.name)
     *
     * @return int[]
     */
    private function findProductIdsByWord(string $word): array
    {
        $pattern = '%' . mb_strtolower($word) . '%';

        // 1. Поиск по полям таблицы products
        $productIds = DB::table('products')
            ->where(function ($query) use ($pattern) {
                foreach (self::PRODUCT_SEARCH_FIELDS as $field) {
                    $query->orWhereRaw('LOWER(' . $field . ') LIKE ?', [$pattern]);
                }
            })
            ->pluck('id')
            ->map(static fn($id) => (int) $id)
            ->toArray();

        // 2. Поиск по названию бренда
        $brandIds = DB::table('products')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->whereRaw('LOWER(brands.name) LIKE ?', [$pattern])
            ->pluck('products.id')
            ->map(static fn($id) => (int) $id)
            ->toArray();

        // 3. Поиск по названиям вариантов атрибутов
        //    attributes_products.value содержит JSON-массив ID вариантов (для атрибутов типа TYPE_VARIANT)
        $attributeIds = DB::table('attributes_products')
            ->join(
                'attribute_variants',
                'attribute_variants.attribute_id',
                '=',
                'attributes_products.attribute_id'
            )
            ->whereRaw('LOWER(attribute_variants.name) LIKE ?', [$pattern])
            ->whereRaw(
                'JSON_CONTAINS(attributes_products.value, CAST(attribute_variants.id AS JSON))'
            )
            ->pluck('attributes_products.product_id')
            ->map(static fn($id) => (int) $id)
            ->toArray();

        return array_unique(array_merge($productIds, $brandIds, $attributeIds));
    }
}
