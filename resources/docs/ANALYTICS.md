# Модуль Analytics — подробная инструкция

Модуль собирает поведенческую аналитику интернет-магазина: посетители, визиты
(сессии), просмотры страниц, действия клиентов, поисковые запросы и пути
клиентов по сайту.

Вся история пишется в **append-only** таблицы с префиксом `analytics_`.
Агрегаты (дневные срезы, популярные запросы) считаются отдельно и служат для
быстрых отчётов.

---

## 1. Архитектура

Модуль построен по принципам Clean Architecture и лежит в
`app/Modules/Analytics`:

| Слой | Путь | Назначение |
|------|------|-----------|
| Domain | `Domain/` | Сущности (`Entities/`), интерфейсы репозиториев (`Interfaces/`), Value Objects (`ValueObjects/`), read-модели (`ReadModels/`), доменные сервисы (`Services/`) |
| Application | `Application/` | Use Cases (`Actions/`), DTO (`DTOs/`), read-запросы (`Queries/`) |
| Infrastructure | `Infrastructure/` | Eloquent-модели (`Models/`), реализации репозиториев (`Persistence/`), сервисы (`Services/`), jobs (`Jobs/`), View Composer (`ViewComposers/`) |
| Presentation | `Presentation/` | HTTP-контроллеры (`Http/Controllers`), middleware (`Http/Middlewares`), blade-шаблон трекера (`resources/views`) |
| БД | `Database/Migrations/` | Миграции таблиц `analytics_*` |

Регистрация модуля — `App\Modules\Analytics\Providers\AnalyticsServiceProvider`
(подключён в `bootstrap/providers.php` первым). Провайдер:
- биндит репозитории (`register()`);
- регистрирует scoped-сервис `VisitorContextInterface`;
- подключает web-маршруты (`routes/web.php`) с middleware-группой `web`;
- подключает views (`analytics::*`), breadcrumbs, migrations.

Слои зависят строго вниз: `Presentation → Application → Domain ← Infrastructure`.

---

## 2. Идентификация посетителя

Ядро аналитики — посетитель (`Visitor`). Анонимного гостя и авторизованного
клиента объединяет постоянный **UUID**, который хранится в cookie.

- Имя cookie: **`user_cookie_id`** (`VisitorUuidGenerator::COOKIE_NAME`).
  Это исторический ключ, уже используемый модулями Shop/Cart.
- UUID генерируется при первом заходе и живёт 1 год
  (`VisitorUuidGenerator::COOKIE_MINUTES = 60 * 24 * 365`).
- `client_id` (из модуля Auth) подставляется после логина и связывает
  анонимную историю с реальным клиентом.

Cookie устанавливается в `IdentifyVisitorMiddleware`, когда в запросе нет
`user_cookie_id`. Маршруты `/analytics/*`, `/admin/*`, `feed/*`, `up`,
`sitemap.xml`, `robots.txt` пропускаются.

---

## 3. Таблицы базы данных

### 3.1 `analytics_visitors` — посетитель (верхнеуровневая сущность)

Объединяет анонимных гостей (по uuid) и авторизованных клиентов (по client_id).

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | Первичный ключ, используется в дочерних таблицах как `visitor_id` |
| `uuid` | CHAR(36) UNIQUE | Постоянный id из cookie, генерируется при первом заходе |
| `client_id` | BIGINT NULL | ID клиента из Auth, появляется после логина |
| `first_visit_at` | TIMESTAMP | Точка отсчёта истории, не меняется |
| `last_visit_at` | TIMESTAMP | Обновляется при каждом визите |
| `visits_count` | INT | Растёт при каждом визите (лояльность) |
| `ip` | VARCHAR(45) | IP первого входа |
| `city` | VARCHAR(100) | Город первого входа (GeoIP) |
| `region` | VARCHAR(100) | Регион первого входа (GeoIP) |
| `country` | VARCHAR(2) | Код страны ISO 3166-1 alpha-2 |
| `user_agent` | VARCHAR(512) | User-Agent первого входа |
| `device_type` | VARCHAR(20) | `desktop` / `mobile` / `tablet` / `bot` |
| `os` | VARCHAR(50) | Операционная система |
| `browser` | VARCHAR(50) | Браузер |
| `referrer` | TEXT | Полный Referer первого входа |
| `source` | VARCHAR(50) | Классификация источника (см. `TrafficSource`) |
| `utm_source` / `utm_medium` / `utm_campaign` / `utm_term` / `utm_content` | VARCHAR(100) | UTM-метки первого URL |
| `landing_url` | VARCHAR(2048) | Полный URL посадочной страницы |
| `client_linked_at` | TIMESTAMP NULL | Момент, когда аноним стал авторизованным |
| `is_bot` | BOOLEAN | Запрос от бота/парсера |
| `created_at` / `updated_at` | TIMESTAMP | Служебные |

Первичные данные (ip, user-agent, source, utm и т.д.) пишутся **один раз** при
создании посетителя и не перезаписываются — это «снимок первого касания».
Префикс `first_` у колонок намеренно убран.

### 3.2 `analytics_sessions` — визит (сессия)

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | Первичный ключ |
| `visitor_id` | BIGINT | Ссылка на `analytics_visitors.id` |
| `started_at` | TIMESTAMP | Начало сессии |
| `last_activity_at` | TIMESTAMP | Последняя активность (обновляется при просмотре/действии) |
| `ended_at` | TIMESTAMP NULL | Завершение (по exit-запросу или таймауту) |
| `duration` | INT NULL | Длительность сессии в секундах |
| `page_views_count` | INT | Количество просмотренных страниц |
| `actions_count` | INT | Количество действий |
| `searches_count` | INT | Количество поисков |
| `entry_url` | TEXT | URL первой страницы сессии |
| `entry_page_type` | VARCHAR(20) | Тип первой страницы |
| `entry_entity_id` | BIGINT NULL | ID сущности первой страницы |
| `exit_url` | TEXT NULL | URL последней страницы |
| `exit_page_type` | VARCHAR(20) NULL | Тип последней страницы |
| `exit_entity_id` | BIGINT NULL | ID сущности последней страницы |
| `referrer` | TEXT NULL | Referer входа |
| `source` | VARCHAR(50) NULL | Классификация источника входа |
| `utm_source` / `utm_medium` / `utm_campaign` / `utm_term` / `utm_content` | VARCHAR(100) NULL | UTM-метки входа (first-touch) |
| `ip` / `city` / `region` / `country` | — | Среда (гео по IP) |
| `user_agent` / `device_type` / `os` / `browser` | — | Среда (устройство) |
| `is_bounce` | BOOLEAN | Отказ: одна страница за сессию без действий |
| `is_bot` | BOOLEAN | Сессия от бота |
| `created_at` / `updated_at` | TIMESTAMP | Служебные |

Активная сессия определяется как запись с `ended_at IS NULL` и
`last_activity_at` не старше таймаута (по умолчанию 30 минут,
`config('analytics.session_timeout_minutes')`).

### 3.3 `analytics_page_views` — просмотр страницы (append-only)

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | Первичный ключ |
| `visitor_id` | BIGINT | Посетитель |
| `session_id` | BIGINT | Сессия |
| `page_type` | VARCHAR(20) | `product` / `category` / `post` / `promo` / `page` / `search` / `home` |
| `entity_id` | BIGINT NULL | ID сущности (товар, категория, запись, акция) |
| `url` | TEXT | Полный URL страницы |
| `path` | VARCHAR(2048) | Путь без домена |
| `title` | VARCHAR(255) NULL | Заголовок страницы |
| `referrer` | TEXT NULL | Referer на момент просмотра |
| `viewed_at` | TIMESTAMP | Дата/время просмотра |
| `duration` | INT NULL | Время на странице (сек), заполняется при выходе |
| `scroll_depth` | TINYINT NULL | Глубина прокрутки 0–100% |
| `is_entry` | BOOLEAN | Первая страница сессии |
| `is_exit` | BOOLEAN | Последняя страница сессии |
| `is_bounce` | BOOLEAN | Единственная страница в сессии |
| `is_bot` | BOOLEAN | Просмотр от бота |
| `created_at` | TIMESTAMP | Служебное |

`duration`, `scroll_depth`, `is_exit` заполняются в момент выхода со страницы
(`RecordExitUseCase`).

### 3.4 `analytics_searches` — поисковый запрос (append-only)

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | Первичный ключ |
| `visitor_id` | BIGINT | Посетитель |
| `session_id` | BIGINT NULL | Сессия |
| `page_view_id` | BIGINT NULL | Просмотр, с которого сделан поиск |
| `query` | VARCHAR(255) | Исходная строка |
| `query_normalized` | VARCHAR(255) | Нормализованная (lower + trim) для агрегации |
| `results_count` | INT | Количество результатов |
| `clicked_result_id` | BIGINT NULL | ID выбранного результата |
| `clicked_result_type` | VARCHAR(20) NULL | Тип выбранного результата (`product` / `category` / `post`) |
| `clicked_position` | SMALLINT NULL | Позиция выбранного результата |
| `searched_at` | TIMESTAMP | Время поиска |
| `is_bot` | BOOLEAN | Поиск от бота |
| `created_at` | TIMESTAMP | Служебное |

### 3.5 `analytics_actions` — действие клиента (append-only)

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | Первичный ключ |
| `visitor_id` | BIGINT | Посетитель |
| `session_id` | BIGINT | Сессия |
| `page_view_id` | BIGINT NULL | Просмотр, в рамках которого совершено действие |
| `action_type` | VARCHAR(50) | Тип действия (см. `ActionType`) |
| `entity_type` | VARCHAR(20) NULL | Тип сущности (см. `EntityType`) |
| `entity_id` | BIGINT NULL | ID сущности |
| `payload` | JSON NULL | Допданные (quantity, price, размер) |
| `occurred_at` | TIMESTAMP | Время действия |
| `is_bot` | BOOLEAN | Действие от бота |
| `created_at` | TIMESTAMP | Служебное |

### 3.6 `analytics_paths` — шаг пути клиента (append-only)

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | Первичный ключ |
| `session_id` | BIGINT | Сессия |
| `visitor_id` | BIGINT | Посетитель |
| `step_number` | SMALLINT | Порядковый номер шага в сессии |
| `page_type` | VARCHAR(20) | Тип страницы |
| `entity_id` | BIGINT NULL | ID сущности |
| `url` | TEXT | URL страницы |
| `duration` | INT NULL | Время на странице (заполняется при выходе) |
| `action_type` | VARCHAR(50) NULL | Действие на шаге (для просмотра — `page_view`) |
| `occurred_at` | TIMESTAMP | Время шага |

### 3.7 `analytics_exits` — точка выхода из сессии

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | BIGINT | Первичный ключ |
| `visitor_id` | BIGINT | Посетитель |
| `session_id` | BIGINT | Сессия |
| `page_view_id` | BIGINT | Последний просмотр |
| `url` | TEXT | URL выхода |
| `page_type` | VARCHAR(20) | Тип страницы выхода |
| `entity_id` | BIGINT NULL | ID сущности |
| `exit_at` | TIMESTAMP | Время выхода |
| `duration_on_page` | INT | Время на последней странице |
| `reason` | VARCHAR(20) NULL | `visibility_hidden` / `pagehide` / `unknown` |

### 3.8 Агрегаты (для отчётов)

**`analytics_popular_searches`** — популярные запросы:
`query_normalized`, `query_sample`, `searches_count`, `unique_visitors_count`,
`clicks_count`, `period_date`, `period_type` (`day` / `week` / `month`),
`updated_at`. Уникальный ключ — `(query_normalized, period_date, period_type)`.

**`analytics_page_daily`** — дневные метрики страниц:
`date`, `page_type`, `entity_id`, `views_count`, `unique_visitors_count`,
`avg_duration`, `bounce_count`, `updated_at`.

**`analytics_sources_daily`** — дневные метрики источников:
`date`, `source`, `utm_source`, `utm_medium`, `utm_campaign`, `sessions_count`,
`unique_visitors_count`, `new_visitors_count`, `bounce_count`, `avg_duration`,
`updated_at`.

---

## 4. Сущности (Domain/Entities)

| Сущность | Таблица | Ключевые поля |
|----------|---------|---------------|
| `VisitorEntity` | `analytics_visitors` | uuid, clientId, firstVisitAt, lastVisitAt, visitsCount, гео/устройство/источник первого входа, clientLinkedAt |
| `SessionEntity` | `analytics_sessions` | startedAt, lastActivityAt, endedAt, duration, счётчики, entry/exit-страницы, utm, среда, isBounce/isBot |
| `PageViewEntity` | `analytics_page_views` | pageType, entityId, url, path, duration, scrollDepth, isEntry/isExit/isBounce/isBot |
| `SearchEntity` | `analytics_searches` | query, queryNormalized, resultsCount, clickedResultId/Type/Position |
| `ActionEntity` | `analytics_actions` | actionType (`ActionType`), entityType (`EntityType`), entityId, payload |
| `PathStepEntity` | `analytics_paths` | stepNumber, pageType, entityId, url, duration, actionType |
| `ExitEntity` | `analytics_exits` | url, pageType, entityId, exitAt, durationOnPage, reason |
| `PageDailyEntity` | `analytics_page_daily` | агрегатные метрики |
| `SourceDailyEntity` | `analytics_sources_daily` | агрегатные метрики |
| `PopularSearchEntity` | `analytics_popular_searches` | агрегатные метрики |

Сущности используют PHP-хуки свойств (get/set) для прозрачной валидации и
инкапсуляции. Ключевые методы доменных сущностей:

- `VisitorEntity::recordVisit()`, `::linkClient()`, `::markAsBot()`;
- `SessionEntity::touch()`, `::registerPageView()`, `::registerAction()`,
  `::registerSearch()`, `::finish($endedAt)` (считает `duration`),
  `::isFinished()`;
- `PageViewEntity::markAsEntry()/markAsExit()/markAsBounce()/markAsBot()`,
  `::trackDuration()`, `::trackScrollDepth()`;
- `SearchEntity::trackClick()`.

---

## 5. Value Objects (Domain/ValueObjects)

| VO | Описание |
|----|----------|
| `VisitorUuid` | UUID посетителя. Валидация формата, `generate()`, `equals()`, `__toString()` |
| `TrafficSource` | Источник трафика. `fromReferrer($referrer, $utm)` классифицирует: `direct`, `google`, `yandex`, `bing`, `mail.ru`, `duckduckgo`, `vk`, `telegram`, `facebook`, `instagram`, `utm`, `internal`, `other` |
| `UtmData` | UTM-метки. `fromQuery($query)` читает `utm_*`; `toArray()` отдаёт ключи `utm_source` и т.д. |
| `DeviceData` | Результат парсинга User-Agent: `deviceType`, `os`, `browser` |
| `ActionType` | Тип действия (закрытый список): `form_submit`, `cart_add`, `cart_remove`, `cart_clear`, `cart_quantity_change`, `checkout_start`, `order_placed`, `one_click_buy`, `register_attempt`, `register`, `login`, `wishlist_add/remove/clear`, `contact_click`, `banner_click`, `search_result_click`. Есть хелперы-группировки `isCart()`, `isWishlist()`, `isAuth()`, `isCheckout()`, `isConversion()` |
| `EntityType` | Тип сущности действия: `product`, `parser.product`, `category`, `room`, `parser.category`, `promotion`, `post`, `page`, `banner`, `form`, `cart`, `order`, `user`, `search` |
| `ContactChannel` | Канал связи: `phone`, `email`, `telegram`, `vk`, `max`, `rutube`, `ok` |

### Классификация `TrafficSource::fromReferrer()`

| Значение | Условие |
|----------|---------|
| `direct` | Referer пустой |
| `google` | `google.` в referrer |
| `yandex` | `yandex.` в referrer |
| `bing` / `mail.ru` / `duckduckgo` | Аналогично по домену |
| `vk` | `vk.com` |
| `telegram` | `t.me` или `telegram.` |
| `facebook` / `instagram` | Соответствующие домены |
| `utm` | Задан `utm_source`, но referrer не распознан |
| `internal` | Referer с того же домена (`app.url`) |
| `other` | Всё остальное |

---

## 6. Application слой (Use Cases)

### Visitor
- **`IdentifyVisitorUseCase::execute(...)`** — находит посетителя по uuid; если
  нет — создаёт нового со снимком первого касания (ip, user-agent, device,
  referrer, source, utm, landing_url). Для существующего — `recordVisit()`
  (инкремент visits_count, обновление last_visit_at) и привязка к client_id.
  Результат кладёт в `VisitorContext`.
- **`LinkVisitorToClientUseCase`** — канонизация/привязка visitor ↔ client_id
  (вызывается из `LinkVisitorToClientJob`).
- **`ResolveGeoIpUseCase::execute(visitorId, city, region, country)`** —
  записывает гео-данные посетителю (вызывается из job при первом касании).

### Session
- **`StartSessionUseCase::execute(...)`** — если есть активная сессия в пределах
  таймаута — продлевает её (`touch`); иначе создаёт новую с entry-страницей,
  source, utm и средой. Для активной сессии дозаполняет пустые utm-метки
  (first-touch, без перезаписи). Кладёт sessionId в `VisitorContext`.

### PageView
- **`TrackPageViewUseCase::execute(...)`** — создаёт запись просмотра (path
  вычисляется из url), помечает первую страницу сессии как entry, инкрементит
  `page_views_count` и обновляет `last_activity_at` сессии, кладёт
  `pageViewId` в `VisitorContext`.
- **`EndPageViewUseCase::execute(pageViewId, duration, scrollDepth, isExit)`** —
  финализирует просмотр (duration/scroll_depth/is_exit).

### Action
- **`TrackActionUseCase::execute(TrackActionData, uuid)`** — резолвит visitor и
  активную сессию (если exit-трекер закрыл старую — создаёт новую через
  `ensureSession()`), создаёт `ActionEntity` (конвертирует `actionType`/`entityType`
  в VO `ActionType`/`EntityType`), инкрементит `actions_count` сессии.
  `page_view_id` берётся из DTO, а если не передан — автоматически подставляется
  последний просмотр текущей сессии (`resolvePageViewId()` →
  `findLastByVisitorInSession`).

### Search
- **`TrackSearchUseCase::execute(visitorId, sessionId, pageViewId, query, resultsCount)`**
  — нормализует строку (lower + trim), пишет `SearchEntity`, инкрементит
  `searches_count` сессии.
- **`RegisterSearchClickUseCase::execute(searchId, resultId, resultType, position)`**
  — дополняет запись поиска данными о клике по результату.
- **`TrackSearchClickUseCase::execute(TrackSearchClickData, uuid)`** — оркестратор:
  сначала `TrackSearchUseCase`, затем `RegisterSearchClickUseCase`.

### Path
- **`RecordPathStepUseCase::execute(...)`** — пишет шаг пути (номер = количество
  шагов сессии + 1). Для просмотра передаётся `actionType = 'page_view'`.

### Exit
- **`RecordExitUseCase::execute(RecordExitData, uuid)`** — находит visitor,
  активную сессию и последний просмотр; финализирует просмотр
  (`finalizeView`), проставляет `duration` последнему шагу пути, помечает
  bounce (если одна страница), закрывает сессию (`ended_at`, `duration`,
  `exit_*`), пишет `ExitEntity`.

---

## 7. Infrastructure слой

### 7.1 Репозитории (Persistence)

Интерфейсы в `Domain/Interfaces`, реализации в `Infrastructure/Persistence`.
Биндинг — в `AnalyticsServiceProvider::register()`.

| Интерфейс | Реализация | Таблица |
|-----------|-----------|---------|
| `VisitorRepositoryInterface` | `VisitorRepository` | `analytics_visitors` |
| `SessionRepositoryInterface` | `SessionRepository` | `analytics_sessions` |
| `PageViewRepositoryInterface` | `PageViewRepository` | `analytics_page_views` |
| `SearchRepositoryInterface` | `SearchRepository` | `analytics_searches` |
| `ActionRepositoryInterface` | `ActionRepository` | `analytics_actions` |
| `PathRepositoryInterface` | `PathRepository` | `analytics_paths` |
| `ExitRepositoryInterface` | `ExitRepository` | `analytics_exits` |
| `PageDailyRepositoryInterface` | `PageDailyRepository` | `analytics_page_daily` |
| `SourceDailyRepositoryInterface` | `SourceDailyRepository` | `analytics_sources_daily` |
| `PopularSearchesRepositoryInterface` | `PopularSearchesRepository` | `analytics_popular_searches` |

Репозитории отвечают за маппинг сущность ↔ Eloquent-модель (`fill`/`hydrate`)
и за запросы для агрегаций. Например, `ActionRepository` конвертирует
`ActionType`/`EntityType` VO в строки БД и обратно.

### 7.2 Eloquent-модели (Infrastructure/Models)

По одной модели на таблицу: `Visitor`, `Session`, `PageView`, `Search`,
`Action`, `Path`, `ExitPoint`, `PageDaily`, `SourceDaily`, `PopularSearch`.
`ExitPoint` назван так, чтобы не конфликтовать с языковой конструкцией `exit`.

### 7.3 Сервисы (Infrastructure/Services)

| Сервис | Назначение |
|--------|-----------|
| `VisitorUuidGenerator` | Генерация UUID и константа имени cookie `user_cookie_id` |
| `UserAgentParser` | Парсинг User-Agent: `deviceType`, `os`, `browser`, `isBot()` |
| `PageTypeResolver` | Определение `page_type` и `entity_id` по маршруту/слагу (`resolve()`, `resolveEntityIdBySlug()`) |
| `GeoIpResolver` | Гео-разрешение по IP (внешний сервис, ip-api.com и т.п.) |
| `VisitorContext` | Scoped-реализация `VisitorContextInterface`: хранит visitor_id/session_id/page_view_id на время HTTP-запроса |

### 7.4 Jobs (Infrastructure/Jobs)

- **`TrackPageViewJob`** — асинхронная часть фиксации просмотра. Сейчас в
  очереди выполняется только **GeoIP-разрешение** (при первом касании, флаг
  `needGeo`). Посетитель/сессия/просмотр/шаг пути создаются синхронно в
  middleware.
- **`LinkVisitorToClientJob`** — асинхронная привязка visitor к client_id после
  логина.

Оба уходят в очередь `analytics` (`->onQueue('analytics')`).

---

## 8. Presentation слой

### 8.1 Middleware

Порядок в web-группе модуля Shop (`ShopServiceProvider::$webMiddlewares`):

1. `InjectClientContextMiddleware` (Shop) — кладёт `ClientContext`.
2. **`IdentifyVisitorMiddleware`** — формирует `VisitSnapshot`
   (uuid, clientId, ip, userAgent, referrer, utm, device, isBot, url,
   isNewUuid) и кладёт в `request->attributes['analytics_snapshot']`.
   При отсутствии cookie — генерирует UUID и ставит cookie в ответ.
3. **`LinkVisitorToClientMiddleware`** — если в снимке есть clientId —
   диспатчит `LinkVisitorToClientJob`.
4. **`TrackPageViewMiddleware`** — синхронно: `IdentifyVisitorUseCase →
   StartSessionUseCase → TrackPageViewUseCase → RecordPathStepUseCase`
   (до `$next`, чтобы `pageViewId` попал в `VisitorContext` к моменту
   рендеринга), затем асинхронно `TrackPageViewJob` (GeoIP). Работает только
   для GET/HEAD, боты пропускаются.

Важно: идентификация/сессия/просмотр выполняются **до** рендеринга ответа —
иначе View Composer не увидит `pageViewId`.

### 8.2 Контроллер и маршруты

`AnalyticsEventController` — тонкий публичный контроллер для событий из JS.
Маршруты (web, без аутентификации, идентификация по cookie):

| Метод | URL | Обработчик | Действие |
|-------|-----|-----------|----------|
| POST | `/analytics/track-action` | `trackAction` | `TrackActionUseCase` |
| POST | `/analytics/record-exit` | `recordExit` | `RecordExitUseCase` |
| POST | `/analytics/track-search-click` | `trackSearchClick` | `TrackSearchClickUseCase` |

DTO (Spatie Laravel Data) валидируются в контроллере через
`Data::validateAndCreate($request->all())`:
- `TrackActionData`: `actionType`, `entityType?`, `entityId?`, `pageViewId?`, `payload?`;
- `RecordExitData`: `url?`, `pageType?`, `duration`, `entityId?`, `pageViewId?`, `scrollDepth?`, `reason?`;
- `TrackSearchClickData`: `query`, `resultId`, `resultType`, `position`, `pageViewId?`.

### 8.3 CSRF

Маршруты `/analytics/*` исключены из CSRF в `app/Http/Middleware/VerifyCsrfToken.php`
(`$except` содержит `'analytics/*'`). Дополнительно JS-трекеры шлют заголовок
`X-CSRF-TOKEN`.

### 8.4 View Composer

`AnalyticsComposer` (зарегистрирован в `ShopServiceProvider::boot()` для видов
`shop.*`, `cart.*`, `cabinet.*`) передаёт в шаблоны:

- `$analyticsUuid` — UUID из cookie `user_cookie_id`;
- `$analyticsPageViewId` — id текущего просмотра из `VisitorContext`.

---

## 9. Фронтенд (JS-трекеры)

### 9.1 Шаблон `tracker.blade.php`

Включается в `resources/views/layouts/main.blade.php` (`@include('analytics::tracker')`).
Выводит глобальные настройки:

```js
window.ANALYTICS_DEBUG = true/false;
window.ANALYTICS_SEARCH_CLICK_URL = '.../analytics/track-search-click';

// по DOMContentLoaded:
window.AnalyticsTracker.init({
    pageViewId: ...,
    uuid: ...,
    exitUrl: route('analytics.record-exit'),
    actionUrl: route('analytics.track-action'),
});
```

URL-ы генерируются через `route()`, чтобы не зависеть от base-path.

### 9.2 `analytics-tracker.js`

Модуль (`@vite('resources/js/nordihome/analytics-tracker.js')`, jQuery
инжектится `@rollup/plugin-inject`). Экспортирует глобальный
`window.AnalyticsTracker`.

- `init(options)` — применяет конфиг, вешает `bindScroll`/`bindExit`/`bindActions`.
- `bindScroll()` — считает `maxScrollDepth` (0–100%).
- `bindExit()` — на `visibilitychange` (hidden) и `pagehide` вызывает
  `sendExit('visibility_hidden' | 'pagehide')`.
- `sendExit(reason)` — собирает `{duration, pageViewId, scrollDepth, reason}`
  и отправляет на `exitUrl` через `fetch(..., { keepalive: true })` (основной
  способ), fallback — `navigator.sendBeacon` с `URLSearchParams`, затем
  `$.post`.
- `bindActions()` — делегирование кликов по `[data-analytics-action]`;
  собирает `actionType`, `entityType`, `entityId`, `payload` и шлёт `trackAction`.
- `trackAction(...)` — POST на `actionUrl` (`/analytics/track-action`).
- `send(url, data)` — обычный POST через jQuery с `X-CSRF-TOKEN`.

### 9.3 `search.js` (отслеживание поиска)

`resources/js/nordihome/search.js` (импортируется в `nordihome.js`). Отвечает за
живой поиск (`pre-search`) и фиксацию кликов по подсказкам.

- `_itemSuggestPresearch(item, type, position)` — рендерит элемент подсказки и
  встраивает в `<a class="js-search-result">` data-атрибуты:
  `data-result-type` (`category`/`product`), `data-result-id`,
  `data-result-position`, `data-search-query`.
- Делегированный обработчик `click` на `.js-search-result` отправляет
  POST `/analytics/track-search-click` (URL из `window.ANALYTICS_SEARCH_CLICK_URL`)
  с `{query, resultId, resultType, position}` через `fetch keepalive`
  (fallback — `sendBeacon`/`$.post`), не блокируя переход по ссылке.

### 9.4 Полный цикл данных

1. **Загрузка страницы**: `IdentifyVisitorMiddleware` ставит cookie
   `user_cookie_id` (если нет); `TrackPageViewMiddleware` синхронно создаёт
   visitor → session → page_view → path_step; `AnalyticsComposer` отдаёт
   `uuid` и `pageViewId` в шаблон; `tracker.blade.php` инициализирует
   `window.AnalyticsTracker`.
2. **Выход/уход**: JS-трекер шлёт `POST /analytics/record-exit` →
   `RecordExitUseCase` (duration/scroll_depth/is_exit просмотра, duration шага
   пути, bounce, закрытие сессии, запись в `analytics_exits`).
3. **Действие**: клик по `[data-analytics-action]` → `POST /analytics/track-action`
   → `TrackActionUseCase` (запись в `analytics_actions`).
4. **Поиск (результаты)**: переход на страницу результатов →
   `ProductController::searchIndex()` вызывает `TrackSearchUseCase`.
5. **Поиск (клик по подсказке)**: клик по `.js-search-result` →
   `POST /analytics/track-search-click` → `TrackSearchClickUseCase`
   (`TrackSearchUseCase` + `RegisterSearchClickUseCase`).
6. **GeoIP**: выполняется асинхронно в `TrackPageViewJob` при первом касании
   посетителя.

---

## 10. Cookie, конфигурация и очереди

- Cookie посетителя: `user_cookie_id` (1 год), используется также в
  `EncryptCookies::$except` и `InjectClientContextMiddleware`.
- Таймаут сессии: `config('analytics.session_timeout_minutes')` — по умолчанию
  **30** минут (значение по умолчанию в use-case, отдельного ключа в
  `config/config.php` нет — при необходимости добавить).
- Очередь: `analytics` (`QUEUE_CONNECTION=database`). Воркер:
  `php artisan queue:work --queue=analytics`.
- После изменения `TrackPageViewJob`/`LinkVisitorToClientJob` нужно
  перезапустить воркер (`php artisan queue:restart`), иначе старый код и
  старые сериализованные задачи могут падать.

---

## 11. Схема связей (кратко)

```
analytics_visitors (1) ──< analytics_sessions (1) ──< analytics_page_views
        │                        │                          │
        └────────< analytics_actions, analytics_searches, analytics_paths, analytics_exits
```

- `analytics_sessions.visitor_id` → `analytics_visitors.id` (cascade delete);
- `analytics_page_views.visitor_id` / `session_id` → родители (cascade);
- `analytics_actions`, `analytics_searches`, `analytics_paths`, `analytics_exits`
  ссылаются на visitor/session/page_view.

---

## 12. Полезные команды

```bash
# Маршруты модуля
php artisan route:list --path=analytics

# Очередь аналитики
php artisan queue:work --queue=analytics
php artisan queue:restart
php artisan queue:clear database --queue=analytics
```

## 13. Полезные файлы (карта модуля)

- Провайдер: `app/Modules/Analytics/Providers/AnalyticsServiceProvider.php`
- Маршруты: `app/Modules/Analytics/routes/web.php`
- Контроллер: `app/Modules/Analytics/Presentation/Http/Controllers/Web/AnalyticsEventController.php`
- Middleware: `app/Modules/Analytics/Presentation/Http/Middlewares/*`
- Трекер: `resources/js/nordihome/analytics-tracker.js`
- Поиск: `resources/js/nordihome/search.js`
- Шаблон: `app/Modules/Analytics/Presentation/resources/views/tracker.blade.php`
- Composer: `app/Modules/Analytics/Infrastructure/ViewComposers/AnalyticsComposer.php`
