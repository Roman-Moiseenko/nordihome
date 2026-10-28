# Аналитика: визуализация в админке (ANALYTICS_VIEW)

> Документ описывает, как устроена визуализация поведенческой аналитики в админке:
> какие данные визуализируются, как они считаются на бэкенде и как отображаются на
> фронтенде (Inertia + Vue 3 + Element Plus + Chart.js).
>
> Модуль аналитики описан в [`ANALYTICS.md`](resources/docs/ANALYTICS.md:1), а
> пожелания по визуализации — в [`show_analytics.txt`](resources/docs/show_analytics.txt:1).
> Общие правила построения модулей — в [`INSTRUCTION.md`](resources/docs/INSTRUCTION.md:1).

---

## 1. Что визуализируется

Все отчёты строятся поверх уже существующих append-only таблиц `analytics_*` и
агрегатов `analytics_popular_searches`, `analytics_page_daily`, `analytics_sources_daily`.
Без доработки JS-трекеров доступны следующие разделы:

| Раздел | Маршрут | Страница Vue | Что показывает |
|--------|---------|--------------|----------------|
| Обзор | `admin.analytics.dashboard` | [`Dashboard/Index.vue`](resources/js/Pages/Analytics/Dashboard/Index.vue:1) | посетители, сессии, просмотры, действия, поиски, % отказов, средняя длительность, динамика по дням, устройства, источники |
| Поиск | `admin.analytics.searches` | [`Search/Index.vue`](resources/js/Pages/Analytics/Search/Index.vue:1) | топ запросов, без результатов, низкий CTR, средняя позиция клика |
| Страницы | `admin.analytics.pages` | [`Page/Index.vue`](resources/js/Pages/Analytics/Page/Index.vue:1) | топ товаров/категорий/статей, время и прокрутка, отказы, входы/выходы |
| Действия | `admin.analytics.actions` | [`Action/Index.vue`](resources/js/Pages/Analytics/Action/Index.vue:1) | сводка по типам, корзина, избранное, баннеры |
| Источники | `admin.analytics.sources` | [`Source/Index.vue`](resources/js/Pages/Analytics/Source/Index.vue:1) | источники, UTM-кампании, конверсия |
| Аудитория | `admin.analytics.audience` | [`Audience/Index.vue`](resources/js/Pages/Analytics/Audience/Index.vue:1) | география, устройства, ОС/браузеры, новые/вернувшиеся |
| Воронка | `admin.analytics.funnel` | [`Funnel/Index.vue`](resources/js/Pages/Analytics/Funnel/Index.vue:1) | просмотр → корзина → заказ, брошенные корзины, топ путей |

**Не реализовано** (нет данных в БД): стоимость привлечения/ROI, ошибки JS/API,
скорость страниц, битые ссылки, look-alike аудитории.

---

## 2. Архитектура потока данных

```
Vue-страница (resources/js/Pages/Analytics/...)
        │  Inertia::render('Analytics/...', ['report' => ..., 'filters' => ...])
        ▼
AnalyticsController (Presentation/Http/Controllers/Web)
        │  AnalyticsFilterData::validateAndCreate() + Query
        ▼
Application Queries (Application/Queries/Report/Get*ReportQuery)
        │  AnalyticsReportRepositoryInterface
        ▼
Infrastructure (AnalyticsReportRepository → Eloquent models → БД)
```

Слои соответствуют Clean Architecture: `Presentation → Application → Domain ← Infrastructure`.
Биндинг интерфейса к реализации выполнен в
[`AnalyticsServiceProvider::register()`](app/Modules/Analytics/Providers/AnalyticsServiceProvider.php:127).

---

## 3. Backend

### 3.1 DTO фильтра периода

[`AnalyticsFilterData`](app/Modules/Analytics/Application/DTOs/Analytics/AnalyticsFilterData.php:1) —
Spatie Laravel Data с полями `dateFrom`, `dateTo` (nullable). Методы
[`getFrom()`](app/Modules/Analytics/Application/DTOs/Analytics/AnalyticsFilterData.php:25) и
[`getTo()`](app/Modules/Analytics/Application/DTOs/Analytics/AnalyticsFilterData.php:36) возвращают
`DateTimeImmutable` с дефолтами «−30 дней … сегодня».

### 3.2 Контракт репозитория

[`AnalyticsReportRepositoryInterface`](app/Modules/Analytics/Domain/Interfaces/AnalyticsReportRepositoryInterface.php:1) —
read-only контракт с одним методом на раздел: `dashboard()`, `search()`, `pages()`,
`actions()`, `sources()`, `audience()`, `funnel()`. Методы возвращают ассоциативные
массивы (read-модели), готовые для сериализации Inertia.

### 3.3 Реализация

[`AnalyticsReportRepository`](app/Modules/Analytics/Infrastructure/Persistence/AnalyticsReportRepository.php:1) —
считает метрики напрямую через Eloquent-модели модуля Analytics и `DB`-фасад.

Особенности:
- Все запросы исключают ботов (`where('is_bot', false)`).
- Агрегаты источников берутся из [`SourceDaily`](app/Modules/Analytics/Infrastructure/Models/SourceDaily.php:1).
- Названия товаров/категорий/статей подставляются в [`topEntityPages()`](app/Modules/Analytics/Infrastructure/Persistence/AnalyticsReportRepository.php:418)
  через [`entityNames()`](app/Modules/Analytics/Infrastructure/Persistence/AnalyticsReportRepository.php:448):
  - товар → [`Product::withTrashed()`](app/Modules/Catalog/Infrastructure/Models/Product.php:117);
  - категория → [`Category`](app/Modules/Catalog/Infrastructure/Models/Category.php:32);
  - пост/статья → [`Post`](app/Modules/Content/Infrastructure/Models/Post.php:26).
  Если сущность не найдена, подставляется `#id`.

### 3.4 Query-классы

Каждый раздел имеет тонкий Query:
- [`GetDashboardReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetDashboardReportQuery.php:1)
- [`GetSearchReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetSearchReportQuery.php:1)
- [`GetPagesReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetPagesReportQuery.php:1)
- [`GetActionsReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetActionsReportQuery.php:1)
- [`GetSourcesReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetSourcesReportQuery.php:1)
- [`GetAudienceReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetAudienceReportQuery.php:1)
- [`GetFunnelReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetFunnelReportQuery.php:1)

### 3.5 Контроллер и маршруты

[`AnalyticsController`](app/Modules/Analytics/Presentation/Http/Controllers/Web/AnalyticsController.php:1) —
тонкий: валидирует фильтр, вызывает Query и возвращает `Inertia::render(...)` с
пропсами `report` и `filters`.

Маршруты объявлены в [`routes/web.php`](app/Modules/Analytics/routes/web.php:17)
в группе `middleware: role:admin|staff`, префикс `admin/analytics`, имя `admin.analytics.`.

Меню — [`menus.php`](app/Modules/Analytics/Presentation/Http/menus.php:1),
хлебные крошки — [`breadcrumbs.php`](app/Modules/Analytics/Presentation/Http/breadcrumbs.php:1).

---

## 4. Frontend

### 4.1 Переиспользуемые компоненты

- [`Chart.vue`](resources/js/VueComponents/Analytics/Chart.vue:1) — обёртка над Chart.js.
  Props: `type` (`line`/`bar`/`doughnut`), `labels`, `datasets`, `height`.
- [`DateRange.vue`](resources/js/Pages/Analytics/Blocks/DateRange.vue:1) — фильтр периода
  (`el-date-picker type="daterange"`), шлёт `router.get(routeName, { dateFrom, dateTo })`
  с `preserveState: true`.

### 4.2 Страницы

Страницы получают пропсы `report` (данные) и `filters` (текущий период) и строятся по
единому шаблону: заголовок + фильтр периода + KPI-карточки + графики + таблицы.

Таблицы топов товаров/категорий/статей выводят колонку `name` с атрибутом
`show-overflow-tooltip` — при нехватке места название обрезается, а полный текст
показывается в всплывающей подсказке при наведении.

---

## 5. Формат данных `report` (контракт фронтенда)

### 5.1 Dashboard
```jsonc
{
  "totals": { "visitors", "sessions", "page_views", "actions", "searches", "bounce_rate", "avg_session_duration" },
  "daily":  [ { "date", "sessions", "visitors", "page_views", "actions", "searches" } ],
  "devices": [ { "name", "sessions", "visitors" } ],
  "sources": [ { "name", "sessions", "visitors" } ]
}
```

### 5.2 Search
```jsonc
{
  "totals": { "searches", "with_clicks", "avg_click_position" },
  "top": [ { "query", "searches", "unique_visitors", "clicks", "ctr", "avg_position" } ],
  "no_results": [ { "query", "total" } ],
  "low_ctr": [ { "query", "searches", "ctr" } ]
}
```

### 5.3 Pages
```jsonc
{
  "metrics": { "views", "avg_duration", "avg_scroll_depth" },
  "top_products": [ { "entity_id", "name", "views", "unique_visitors", "avg_duration", "avg_scroll_depth" } ],
  "top_categories": [ { "entity_id", "name", "views", "unique_visitors", "avg_duration", "avg_scroll_depth" } ],
  "top_posts": [ { "entity_id", "name", "views", "unique_visitors", "avg_duration", "avg_scroll_depth" } ],
  "high_bounce": [ { "page_type", "entity_id", "bounces" } ],
  "entry_pages": [ { "page_type", "entity_id", "total" } ],
  "exit_pages": [ { "page_type", "entity_id", "total" } ]
}
```

### 5.4 Actions
```jsonc
{
  "totals": [ { "action_type", "total", "unique_visitors", "entities" } ],
  "cart_adds": [ { "entity_id", "total", "unique_visitors" } ],
  "cart_removes": [ { "entity_id", "total", "unique_visitors" } ],
  "wishlist_adds": [ { "entity_id", "total", "unique_visitors" } ],
  "banner_clicks": [ { "entity_id", "total", "unique_visitors" } ]
}
```

### 5.5 Sources
```jsonc
{
  "sources": [ { "source", "sessions", "unique_visitors", "new_visitors", "bounces", "avg_duration", "conversion" } ],
  "utm_campaigns": [ { "campaign", "source", "sessions", "visitors" } ]
}
```

### 5.6 Audience
```jsonc
{
  "cities": [ { "name", "total" } ],
  "regions": [ { "name", "total" } ],
  "countries": [ { "name", "total" } ],
  "devices": [ { "name", "total" } ],
  "os": [ { "name", "total" } ],
  "browsers": [ { "name", "total" } ],
  "new_vs_returning": [ { "name", "total" } ]
}
```

### 5.7 Funnel
```jsonc
{
  "stages": [ { "stage", "sessions" } ],
  "abandoned_carts": 12,
  "top_paths": [ { "path", "count" } ]
}
```

---

## 6. Как добавить новый раздел отчёта

1. Добавить метод в [`AnalyticsReportRepositoryInterface`](app/Modules/Analytics/Domain/Interfaces/AnalyticsReportRepositoryInterface.php:1)
   и реализацию в [`AnalyticsReportRepository`](app/Modules/Analytics/Infrastructure/Persistence/AnalyticsReportRepository.php:1).
2. Создать Query в [`Application/Queries/Report`](app/Modules/Analytics/Application/Queries/Report) (образец — [`GetDashboardReportQuery`](app/Modules/Analytics/Application/Queries/Report/GetDashboardReportQuery.php:1)).
3. Добавить метод в [`AnalyticsController`](app/Modules/Analytics/Presentation/Http/Controllers/Web/AnalyticsController.php:1).
4. Добавить маршрут в [`routes/web.php`](app/Modules/Analytics/routes/web.php:17),
   пункт меню в [`menus.php`](app/Modules/Analytics/Presentation/Http/menus.php:1)
   и крошку в [`breadcrumbs.php`](app/Modules/Analytics/Presentation/Http/breadcrumbs.php:1).
5. Создать Vue-страницу в [`resources/js/Pages/Analytics`](resources/js/Pages/Analytics).

---

## 7. Замеченные особенности

- Названия сущностей резолвятся по `entity_id` после агрегации, чтобы не тянуть
  отношения в основной запрос. Для удалённых товаров используется `withTrashed()`.
- Конверсия по источникам считается как «сессии с `order_placed` / сессии источника»,
  данные берутся из `analytics_actions` через join с `analytics_sessions`.
- Агрегаты `analytics_page_daily` и `analytics_popular_searches` в текущей визуализации
  не задействованы напрямую: детальные метрики считаются из append-only таблиц, что
  позволяет гибко менять период.
