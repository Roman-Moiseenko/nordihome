<?php
namespace App\Modules\Shop\Infrastructure\Persistence;
use Illuminate\Support\Facades\Cache;
class CacheInvalidationRegistry
{
    //Категории
    public const string CATEGORY_TREE = 'category_tree';
    public const string CATEGORY_PRODUCTS_ID = 'category_products_{id}';
    public const string CATEGORY_FILTERS_ID = 'category_filters_{id}';
    public const string CATEGORY_INDEX_PAGE = 'category_index_page';
    private const array CATEGORY_KEYS = [
        self::CATEGORY_TREE,
        self::CATEGORY_PRODUCTS_ID,
        self::CATEGORY_INDEX_PAGE,
        self::CATEGORY_FILTERS_ID,

    ];

    //Комнаты
    public const string ROOM_TREE = 'room_tree';
    public const string ROOM_PRODUCTS_ID = 'room_products_{id}';
    public const string ROOM_FILTERS_ID = 'room_filters_{id}';
    public const string ROOM_INDEX_PAGE = 'room_index_page';
    private const array ROOM_KEYS = [
        self::ROOM_TREE,
        self::ROOM_PRODUCTS_ID,
        self::ROOM_INDEX_PAGE,
        self::ROOM_FILTERS_ID,
    ];

    //Каталог Икеа
    public const string IKEA_CATEGORY_INDEX_PAGE = 'ikea_tree';
    public const string IKEA_PRODUCTS_ID = 'ikea_products_{id}';
    private const array IKEA_CATEGORY_KEYS = [
        self::IKEA_CATEGORY_INDEX_PAGE,
        self::IKEA_PRODUCTS_ID,
    ];

    //Акции
    public const string PROMOTION_PRODUCTS_ID = 'promotion_products_{id}';
    public const string PROMOTION_FILTERS_ID = 'promotion_filters_{id}';
    private const array PROMOTION_PRODUCTS_KEYS = [
        self::PROMOTION_PRODUCTS_ID,
        self::PROMOTION_FILTERS_ID,
    ];


    public const string MENUS = 'menus';
    public const string CONTACTS = 'contacts';


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
    public function forgetMenus(): void
    {
        Cache::forget(self::MENUS);
    }

    public function forgetContacts(): void
    {
        Cache::forget(self::CONTACTS);
    }

    public function forgetPromotion(int $promotionId): void
    {
        foreach (self::PROMOTION_PRODUCTS_KEYS as $key) {
            $resolvedKey = str_replace('{id}', $promotionId, $key);
            \Log::info($resolvedKey);

            Cache::forget($resolvedKey);
        }
    }
}
