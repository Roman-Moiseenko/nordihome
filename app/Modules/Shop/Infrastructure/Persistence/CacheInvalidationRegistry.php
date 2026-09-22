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


    //Фиды (выгрузки Google/Yandex) — зависят от товаров, категорий, комнат, групп и акций.
    //Теги не используем (не все драйверы кеша их поддерживают), поэтому инвалидация
    //делается через инкремент версии, которая входит в ключ выгрузки.
    public const string FEED_VERSION = 'feed_version';
    public const string FEED_EXPORT = 'feed_export_{id}_v{version}';


    //Карта сайта (sitemap.xml) — зависит от товаров, категорий, комнат,
    //страниц, новостей и акций.
    public const string SITEMAP = 'sitemap';

    public const string MENUS = 'menus';
    public const string CONTACTS = 'contacts';

    //Страницы (контентные страницы) — кешируются по slug.
    public const string PAGE_BY_SLUG = 'page_{slug}';


    /**
     * Сбросить все ключи, связанные с категорией (включая глобальные).
     */
    public function forgetCategory(int $categoryId): void
    {
        foreach (self::CATEGORY_KEYS as $key) {
            $resolvedKey = str_replace('{id}', $categoryId, $key);
            Cache::forget($resolvedKey);
        }
        $this->forgetFeeds();
        $this->forgetSitemap();
    }

    public function forgetRoom(int $categoryId): void
    {
        foreach (self::ROOM_KEYS as $key) {
            $resolvedKey = str_replace('{id}', $categoryId, $key);
            Cache::forget($resolvedKey);
        }
        $this->forgetFeeds();
        $this->forgetSitemap();
    }

    public function forgetIkeaCategory(int $categoryId): void
    {
        foreach (self::IKEA_CATEGORY_KEYS as $key) {
            $resolvedKey = str_replace('{id}', $categoryId, $key);
            Cache::forget($resolvedKey);
        }
        $this->forgetFeeds();
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
            Cache::forget($resolvedKey);
        }
        $this->forgetFeeds();
        $this->forgetSitemap();
    }

    /**
     * Сброс всех кешей выгрузок фидов (товары, категории, комнаты, группы, акции).
     * Инкрементируем версию — старые ключи выгрузок перестают использоваться,
     * а с истечением TTL удаляются автоматически.
     */
    public function forgetFeeds(): void
    {
        $version = (int) Cache::get(self::FEED_VERSION, 0);
        Cache::put(self::FEED_VERSION, $version + 1, now()->addYear());
    }

    /**
     * Сброс кеша карты сайта.
     */
    public function forgetSitemap(): void
    {
        Cache::forget(self::SITEMAP);
    }

    /**
     * Сброс кеша контентной страницы по её slug.
     * Страница также участвует в карте сайта, поэтому сбрасываем и её.
     */
    public function forgetPage(string $slug): void
    {
        Cache::forget(str_replace('{slug}', $slug, self::PAGE_BY_SLUG));
        $this->forgetSitemap();
    }

    /**
     * Полный сброс глобальных кешей модуля (деревья, индексные страницы,
     * меню, контакты, карта сайта и версия фидов).
     *
     * Ключи с параметром {id} сбрасываются отдельными методами
     * (forgetCategory/forgetRoom/forgetIkeaCategory/forgetPromotion/forgetPage),
     * поскольку здесь нет перечня идентификаторов.
     */
    public function forgetAll(): void
    {
        foreach ([
            self::CATEGORY_TREE,
            self::CATEGORY_INDEX_PAGE,
            self::ROOM_TREE,
            self::ROOM_INDEX_PAGE,
            self::IKEA_CATEGORY_INDEX_PAGE,
        ] as $key) {
            Cache::forget($key);
        }

        $this->forgetMenus();
        $this->forgetContacts();
        $this->forgetFeeds();
        $this->forgetSitemap();
    }
}
