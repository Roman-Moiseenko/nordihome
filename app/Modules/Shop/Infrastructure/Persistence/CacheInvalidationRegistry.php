<?php

namespace App\Modules\Shop\Infrastructure\Persistence;
use Illuminate\Support\Facades\Cache;
class CacheInvalidationRegistry
{
    public const string CATEGORY_TREE = 'category_tree';
    public const string CATEGORY_PRODUCTS_ID = 'category_products_{id}';
    public const string CATEGORY_INDEX_PAGE = 'category_index_page';


    private const array CATEGORY_KEYS = [
        self::CATEGORY_TREE,
        self::CATEGORY_PRODUCTS_ID,
        self::CATEGORY_INDEX_PAGE,
    ];
    public const string ROOM_TREE = 'room_tree';
    public const string ROOM_PRODUCTS_ID = 'room_products_{id}';
    public const string ROOM_INDEX_PAGE = 'room_index_page';
    private const array ROOM_KEYS = [
        self::ROOM_TREE,
        self::ROOM_PRODUCTS_ID,
        self::ROOM_INDEX_PAGE,
    ];

    public const string IKEA_CATEGORY_INDEX_PAGE = 'category_ikea_tree';
    private const array IKEA_CATEGORY_KEYS = [
        self::IKEA_CATEGORY_INDEX_PAGE,

    ];

    /**
     * Сбросить все ключи, связанные с категорией (включая глобальные).
     */
    public function forgetCategory(int $categoryId): void
    {
        foreach (self::CATEGORY_KEYS as $key) {
            $resolvedKey = str_replace('{id}', $categoryId, $key);
            Cache::forget($resolvedKey);
        }
    }

    public function forgetRoom(int $categoryId): void
    {
        foreach (self::ROOM_KEYS as $key) {
            $resolvedKey = str_replace('{id}', $categoryId, $key);
            Cache::forget($resolvedKey);
        }
    }

    public function forgetIkeaCategory(int $categoryId): void
    {
        foreach (self::IKEA_CATEGORY_KEYS as $key) {
            $resolvedKey = str_replace('{id}', $categoryId, $key);
            Cache::forget($resolvedKey);
        }
    }

}
