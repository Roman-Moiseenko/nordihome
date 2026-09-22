# Кеширование в модуле Shop

Документ описывает, как устроено кеширование запросов в модуле [`Shop`](../app/Modules/Shop) и как
выполняется его сброс (инвалидация).

---

## 1. Общая схема

Модуль Shop кеширует «тяжёлые» данные витрины через фасад `Illuminate\Support\Facades\Cache`
(драйвер настраивается в [`config/cache.php`](../config/cache.php)):

- деревья категорий/комнат/ИКЕА;
- списки идентификаторов товаров и фильтры категорий/комнат/акций;
- индексные страницы каталога;
- меню и контакты;
- карту сайта (`sitemap`);
- выгрузки фидов (Google/Yandex) через версию ключа;
- контентные страницы (новое — по `slug`).

Единый источник ключей и методов сброса — класс
[`CacheInvalidationRegistry`](../app/Modules/Shop/Infrastructure/Persistence/CacheInvalidationRegistry.php:5).
Он не использует теги (не все драйверы кеша их поддерживают): сброс выполняется либо `Cache::forget()`
по конкретному ключу, либо инкрементом версии (для фидов).

---

## 2. Ключи кеша

Все ключи объявлены константами в
[`CacheInvalidationRegistry`](../app/Modules/Shop/Infrastructure/Persistence/CacheInvalidationRegistry.php:5).

| Группа            | Константа                         | Значение                  | Кто читает                                                                                                  |
|-------------------|-----------------------------------|---------------------------|-------------------------------------------------------------------------------------------------------------|
| Категории         | `CATEGORY_TREE`                   | `category_tree`           | [`GetCategoryTreeQuery`](../app/Modules/Shop/Application/Queries/Category/GetCategoryTreeQuery.php:25)      |
| Категории         | `CATEGORY_PRODUCTS_ID`            | `category_products_{id}`  | [`CategoryPageQuery`](../app/Modules/Shop/Application/Queries/Category/CategoryPageQuery.php:59)             |
| Категории         | `CATEGORY_FILTERS_ID`             | `category_filters_{id}`   | [`CategoryPageQuery`](../app/Modules/Shop/Application/Queries/Category/CategoryPageQuery.php:244)            |
| Категории         | `CATEGORY_INDEX_PAGE`             | `category_index_page`     | [`CategoryIndexQuery`](../app/Modules/Shop/Application/Queries/Category/CategoryIndexQuery.php:30)           |
| Комнаты           | `ROOM_TREE`                       | `room_tree`               | [`GetRoomTreeQuery`](../app/Modules/Shop/Application/Queries/Room/GetRoomTreeQuery.php:27)                  |
| Комнаты           | `ROOM_PRODUCTS_ID`                | `room_products_{id}`      | [`RoomPageQuery`](../app/Modules/Shop/Application/Queries/Room/RoomPageQuery.php:56)                         |
| Комнаты           | `ROOM_FILTERS_ID`                 | `room_filters_{id}`       | [`RoomPageQuery`](../app/Modules/Shop/Application/Queries/Room/RoomPageQuery.php:158)                        |
| Комнаты           | `ROOM_INDEX_PAGE`                 | `room_index_page`         | [`RoomIndexQuery`](../app/Modules/Shop/Application/Queries/Room/RoomIndexQuery.php:29)                       |
| ИКЕА              | `IKEA_CATEGORY_INDEX_PAGE`        | `ikea_tree`               | [`IkeaIndexQuery`](../app/Modules/Shop/Application/Queries/Ikea/IkeaIndexQuery.php:27) и другие               |
| ИКЕА              | `IKEA_PRODUCTS_ID`                | `ikea_products_{id}`      | [`IkeaViewQuery`](../app/Modules/Shop/Application/Queries/Ikea/IkeaViewQuery.php:47)                         |
| Акции             | `PROMOTION_PRODUCTS_ID`           | `promotion_products_{id}` | [`PromotionPageQuery`](../app/Modules/Shop/Application/Queries/Promotion/PromotionPageQuery.php:51)          |
| Акции             | `PROMOTION_FILTERS_ID`            | `promotion_filters_{id}`  | [`PromotionPageQuery`](../app/Modules/Shop/Application/Queries/Promotion/PromotionPageQuery.php:145)         |
| Фиды              | `FEED_VERSION`                    | `feed_version`            | выгрузки Google/Yandex                                                                                       |
| Фиды              | `FEED_EXPORT`                     | `feed_export_{id}_v{version}` | выгрузки Google/Yandex                                                                                   |
| Sitemap           | `SITEMAP`                         | `sitemap`                 | генератор карты сайта                                                                                        |
| Меню              | `MENUS`                           | `menus`                   | [`GetMenusQuery`](../app/Modules/Shop/Application/Queries/Menu/GetMenusQuery.php:25)                         |
| Контакты          | `CONTACTS`                        | `contacts`                | [`GetContactsQuery`](../app/Modules/Shop/Application/Queries/Menu/GetContactsQuery.php:33)                   |
| Страницы          | `PAGE_BY_SLUG`                    | `page_{slug}`             | [`PageViewQuery`](../app/Modules/Shop/Application/Queries/Page/PageViewQuery.php:25)                         |

---

## 3. Как кешируются запросы

### 3.1. Простой `Cache::remember`

Используется для данных, которые строятся одним вызовом репозитория:

```php
// GetMenusQuery::execute()
return Cache::remember(
    CacheInvalidationRegistry::MENUS,
    now()->addDay(),
    fn(): array => $this->repository->getMenusWithItems(),
);
```

### 3.2. `Cache::remember` с ключом по идентификатору

```php
// CategoryPageQuery::execute()
$key_cache = str_replace('{id}', (string)$mainInfo->id, CacheInvalidationRegistry::CATEGORY_PRODUCTS_ID);

$allProductIds = Cache::remember(
    $key_cache,
    now()->addDay(),
    fn() => $this->repository->getProductIdsInCategory($mainInfo->id),
);
```

### 3.3. Ручное чтение + блокировка (для деревьев)

Для деревьев используется блокировка `Cache::lock()`, чтобы при «холодном» старте дерево не собиралось
несколько раз одновременно:

```php
// GetRoomTreeQuery::execute()
if ($cached = Cache::get(self::CACHE_KEY)) {
    return $cached;
}

$lock = Cache::lock('build_' . self::CACHE_KEY, 10);
try {
    if ($lock->block(5)) {
        if ($cached = Cache::get(self::CACHE_KEY)) {
            return $cached;
        }
        $tree = $this->repository->getFullTree();
        Cache::put(self::CACHE_KEY, $tree, now()->addDay());
        return $tree;
    }
} finally {
    optional($lock)->release();
}

return $this->repository->getFullTree();
```

### 3.4. Кеширование контентных страниц по `slug` (добавлено)

[`PageViewQuery`](../app/Modules/Shop/Application/Queries/Page/PageViewQuery.php:23) кеширует готовый
объект `PageViewPageData` под ключом `page_{slug}`:

```php
$key = str_replace('{slug}', $slug, CacheInvalidationRegistry::PAGE_BY_SLUG);

$cached = Cache::get($key);
if ($cached instanceof PageViewPageData) {
    return $cached;
}

$pageData = $this->build($slug);

// null (страница не найдена) не кешируется
if ($pageData instanceof PageViewPageData) {
    Cache::put($key, $pageData, now()->addDay());
}

return $pageData;
```

Особенности:

- ключ зависит от `slug` страницы;
- TTL — одни сутки (`now()->addDay()`);
- отсутствующая страница (`null`) **не** кешируется, чтобы после создания страницы
  данные не отдавались из устаревшего кеша.

---

## 4. Как устроен сброс (инвалидация)

### 4.1. `CacheInvalidationRegistry`

Содержит методы точечного сброса:

| Метод                              | Что сбрасывает                                                         |
|------------------------------------|------------------------------------------------------------------------|
| `forgetCategory(int $id)`          | ключи категории (`tree`, `products`, `filters`, `index`) + фиды + sitemap |
| `forgetRoom(int $id)`              | ключи комнаты + фиды + sitemap                                         |
| `forgetIkeaCategory(int $id)`      | ключи ИКЕА-категории + фиды                                            |
| `forgetPromotion(int $id)`         | ключи акции + фиды + sitemap                                           |
| `forgetPage(string $slug)`         | `page_{slug}` + sitemap                                                |
| `forgetMenus()`                    | `menus`                                                                |
| `forgetContacts()`                 | `contacts`                                                             |
| `forgetFeeds()`                    | инкремент `feed_version` (старые выгрузки перестают использоваться)     |
| `forgetSitemap()`                  | `sitemap`                                                              |
| `forgetAll()`                      | глобальные ключи (деревья, индексы, меню, контакты, фиды, sitemap)      |

Важно: `forgetFeeds()` не перебирает ключи выгрузок, а увеличивает счётчик версии. Ключ выгрузки
содержит версию (`feed_export_{id}_v{version}`), поэтому после инкремента старый ключ просто перестаёт
читаться, а с истечением TTL удаляется автоматически.

### 4.2. Обсерверы моделей

Автоматический сброс при изменении сущностей реализован обсерверами в
[`app/Modules/Shop/Infrastructure/Observers`](../app/Modules/Shop/Infrastructure/Observers) и подключается в
[`ShopServiceProvider::boot()`](../app/Modules/Shop/Providers/ShopServiceProvider.php:122):

| Обсервер                        | Модель          | Вызываемый сброс             |
|---------------------------------|-----------------|------------------------------|
| `CategoryCacheObserver`         | `Category`      | `forgetCategory($id)`        |
| `RoomCacheObserver`             | `Room`          | `forgetRoom($id)`            |
| `IkeaCategoryCacheObserver`     | `ParserCategory`| `forgetIkeaCategory($id)`    |
| `MenuCacheObserver`             | `Menu`          | `forgetMenus()`              |
| `MenuItemCacheObserver`         | `MenuItem`      | `forgetMenus()`              |
| `ContactCacheObserver`          | `Contact`       | `forgetContacts()`           |
| `PromotionProductCacheObserver` | `Promotion`     | `forgetPromotion($id)`       |
| `FeedCacheObserver`             | `Feed`          | сброс фидов                  |
| `ProductCacheObserver`          | `Product`       | `forgetSitemap()`            |
| `PageCacheObserver`             | `Page`          | `forgetPage($slug)`          |
| `PostCacheObserver`             | `Post`          | сброс новостей/sitemap       |

---

## 5. Команда полного сброса кеша

Для ручного сброса всех кешей модуля Shop используется команда
[`ForgetShopCacheCommand`](../app/Modules/Shop/Console/Commands/ForgetShopCacheCommand.php:12):

```bash
php artisan shop:cache-clear
```

Команда по очереди сбрасывает:

1. кеши категорий;
2. кеши комнат;
3. кеши ИКЕА-категорий;
4. кеши акций;
5. кеши контентных страниц (по `slug`);
6. глобальные кеши (деревья, индексные страницы, меню, контакты, фиды, sitemap).

Команда зарегистрирована в
[`ShopServiceProvider::registerCommands()`](../app/Modules/Shop/Providers/ShopServiceProvider.php:158).

---

## 6. Прогрев (создание) всех кешей

Для предзаполнения кешей после сброса существует команда:

```bash
php artisan shop:cache-warm
```

Команда [`WarmShopCacheCommand`](../app/Modules/Shop/Console/Commands/WarmShopCacheCommand.php:10)
диспатчит фоновую задачу [`WarmShopCacheJob`](../app/Modules/Shop/Infrastructure/Jobs/WarmShopCacheJob.php:30)
в очередь `photo` (самая низкоприоритетная, идёт последней), чтобы прогрев не нагружал
веб-запросы и не конкурировал с более важными задачами.

Задача по очереди заполняет кеши:

1. глобальные — деревья категорий/комнат/ИКЕА, индексные страницы, меню;
2. страницы категорий (товары + фильтры);
3. страницы комнат (товары + фильтры);
4. страницы ИКЕА-категорий (товары);
5. страницы акций (товары + фильтры);
6. контентные страницы (по `slug`).

Каждый этап обёрнут в `try/catch`: сбой одного элемента логируется и не прерывает прогрев.

Очередь задаётся двумя способами:
- свойством `public string $queue = QueueName::PHOTO` в job;
- явным `->onQueue(QueueName::PHOTO)` при диспатче в команде.

---

## 7. Имена очередей (Shared)

Имена очередей вынесены в readonly VO
[`QueueName`](../app/Modules/Shared/Domain/ValueObjects/QueueName.php:12) модуля Shared:

| Константа   | Значение     |
|-------------|--------------|
| `ANALYTICS` | `analytics`  |
| `DEFAULT`   | `default`    |
| `PHOTO`     | `photo`      |

Список всех значений доступен в константе `QueueName::ALL`.
