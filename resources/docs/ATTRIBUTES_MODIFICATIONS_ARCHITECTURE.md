# Архитектура системы атрибутов (Attribute) и модификаций (Modification)

> Модуль: `App\Modules\Catalog`
> Цель документа — дать целостное представление о том, как устроены «атрибуты» и «модификации товаров», какие слои кода задействованы, как хранятся данные в БД и в каком виде они отдаются на фронтенд.

---

## 1. Общая картина: два стиля архитектуры в одном модуле

Модуль `Catalog` исторически развивался, поэтому внутри него **сосуществуют два подхода**:

### 1.1. Новая «Clean Architecture» (Domain / Application / Infrastructure)

Используется для **CRUD атрибутов** и их привязки к категориям, а после рефакторинга — и для **модификаций**. Классические слои:

- **Domain** — [`Domain/Entities/AttributeEntity.php`](app/Modules/Catalog/Domain/Entities/AttributeEntity.php:10), [`Domain/Entities/AttributeVariantEntity.php`](app/Modules/Catalog/Domain/Entities/AttributeVariantEntity.php:7), [`Domain/ValueObjects/AttributeType.php`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:9), интерфейсы репозиториев в [`Domain/Interfaces/`](app/Modules/Catalog/Domain/Interfaces/AttributeRepositoryInterface.php:12).
- **Application** — сценарии (`Actions/Attribute/*`, `Actions/AttributeCategory/*`), DTO на базе `Spatie\LaravelData` (`DTOs/Attribute/*`), сервисы (`Application/Services/*`).
- **Infrastructure** — реализация репозиториев ([`Infrastructure/Persistence/AttributeRepository.php`](app/Modules/Catalog/Infrastructure/Persistence/AttributeRepository.php:21)) поверх Eloquent-моделей [`Infrastructure/Models/Attribute.php`](app/Modules/Catalog/Infrastructure/Models/Attribute.php:26).

Для модификаций этот слой представлен:

- Domain-сущности [`ModificationEntity`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:12), [`ModificationProductEntity`](app/Modules/Catalog/Domain/Entities/ModificationProductEntity.php:12), Value Objects [`ModificationAttributes`](app/Modules/Catalog/Domain/ValueObjects/ModificationAttributes.php:25), [`ModificationValues`](app/Modules/Catalog/Domain/ValueObjects/ModificationValues.php:7), интерфейс [`ModificationRepositoryInterface`](app/Modules/Catalog/Domain/Interfaces/ModificationRepositoryInterface.php:11).
- Application-сценарии [`CreateModificationUseCase`](app/Modules/Catalog/Application/Actions/Modification/CreateModificationUseCase.php:20), [`IndexModificationQuery`](app/Modules/Catalog/Application/Actions/Modification/IndexModificationQuery.php:14), [`SearchModificationCreateQuery`](app/Modules/Catalog/Application/Actions/Modification/SearchModificationCreateQuery.php:16) и сервис [`ModificationValuesResolver`](app/Modules/Catalog/Application/Services/ModificationValuesResolver.php:9).
- Infrastructure-реализация [`ModificationRepository`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:16) поверх моделей `Modification`, `ModificationProduct`, `ModificationAttribute`, `ModificationProductValue`.

### 1.2. «Легаси»-слой (прямые Eloquent-модели + Repository + Service)

Остался для **части старых эндпоинтов** модификаций (карточка, переименование, удаление, смена базового, добавление/удаление товара) и для части атрибутов. Здесь Eloquent-модель является одновременно и «сущностью»:

- [`Entity/Modification.php`](app/Modules/Catalog/Entity/Modification.php:23) — Eloquent-модель `modifications` (легаси, постепенно заменяется на `Infrastructure/Models/Modification`).
- [`Entity/ModificationProduct.php`](app/Modules/Catalog/Entity/ModificationProduct.php:14) — pivot-модель `modifications_products` (легаси).
- [`Repository/ModificationRepository.php`](app/Modules/Catalog/Repository/ModificationRepository.php:15) — чтение/сериализация для карточки (`ModificationWithToArray`).
- [`Service/ModificationService.php`](app/Modules/Catalog/Service/ModificationService.php:11) — легаси бизнес-логика (rename, delete, addProduct, delProduct, setBase).
- [`Repository/AttributeRepository.php`](app/Modules/Catalog/Repository/AttributeRepository.php:13) — «старый» репозиторий атрибутов (списки, поиск).
- Контроллер [`Controllers/ModificationController.php`](app/Modules/Catalog/Controllers/ModificationController.php).

> **Ключевой вывод для анализа:** атрибуты продублированы в двух мирах (новый CRUD через Clean Architecture + старый репозиторий/сервисы). Модификации переведены на Clean Architecture в части **создания, индекса и поиска**; карточка и операции изменения (rename/delete/set-base/add-product/del-product) пока используют легаси-слой.

---

## 2. Подсистема «Атрибуты»

### 2.1. Модель данных (таблицы)

#### `attribute_groups` — группы атрибутов

| Поле    | Тип            | Примечание |
|---------|----------------|------------|
| `id`    | bigint PK      |            |
| `name`  | string, unique | Название группы |
| `sort`  | int            | Добавлено [`2023_10_25_213550_add_sort_attribute_group_table.php`](app/Modules/Catalog/Database/Migrations/2023_10_25_213550_add_sort_attribute_group_table.php) |
| `svg`   | text, nullable | Иконка-разметка, добавлено [`2026_10_06_123512_update_attribute_groups_table.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_123512_update_attribute_groups_table.php:14) |

Создание: [`2023_10_23_142356_create_attribute_groups_table.php`](app/Modules/Catalog/Database/Migrations/2023_10_23_142356_create_attribute_groups_table.php:14).

#### `attributes` — сами атрибуты

| Поле       | Тип              | Примечание |
|------------|------------------|------------|
| `id`       | bigint PK        |            |
| `group_id` | FK → `attribute_groups` | `onDelete restrict` |
| `name`     | string           | Название атрибута |
| `multiple` | bool, default false | Множественный выбор |
| `sameAs`   | string, default '' | Ссылка на «аналог» |
| `filter`   | bool, default false | Участвует в фильтре |
| `type`     | string           | Изначально `integer` (коды 101–106), с [`2026_10_06_160000_update_attributes_table_type_to_string.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_160000_update_attributes_table_type_to_string.php:16) — строка |
| `sort`     | int, default 0   |            |
| `widget`   | string, default '' | Виджет отображения |
| `show_in`  | bool, default false | Показывать в карточке, добавлено [`2023_10_26_114500_add_field_show_in_attributes_table.php`](app/Modules/Catalog/Database/Migrations/2023_10_26_114500_add_field_show_in_attributes_table.php:14) |

Создание: [`2023_10_23_142441_create_attributes_table.php`](app/Modules/Catalog/Database/Migrations/2023_10_23_142441_create_attributes_table.php:14).

#### `attribute_variants` — варианты значения атрибута

| Поле           | Тип          | Примечание |
|----------------|--------------|------------|
| `id`           | bigint PK    |            |
| `attribute_id` | FK → `attributes` | `onDelete cascade` |
| `name`         | string       | Название варианта |
| `slug`         | string       | `Str::slug(name)` |

Создание: [`2023_10_23_142616_create_attribute_variants_table.php`](app/Modules/Catalog/Database/Migrations/2023_10_23_142616_create_attribute_variants_table.php:14).

#### `attributes_products` — значения атрибутов у товара (pivot)

| Поле         | Тип   | Примечание |
|--------------|-------|------------|
| `attribute_id` | FK → `attributes` | `onDelete restrict` |
| `product_id`   | FK → `products` | было `restrict`, стало `cascade` в [`2024_06_05_180931_update_attributes_products_table.php`](app/Modules/Catalog/Database/Migrations/2024_06_05_180931_update_attributes_products_table.php:20) |
| `value`        | json  | Значение (см. §2.3) |

Создание: [`2023_10_23_161657_create_attributes_products_table.php`](app/Modules/Catalog/Database/Migrations/2023_10_23_161657_create_attributes_products_table.php:14).

#### `attributes_categories` — привязка атрибутов к категориям (pivot)

| Поле          | Тип   | Примечание |
|---------------|-------|------------|
| `attribute_id` | FK → `attributes` | было `restrict`, стало `cascade` в [`2026_09_08_150000_update_attributes_categories_foreign_keys_cascade.php`](app/Modules/Catalog/Database/Migrations/2026_09_08_150000_update_attributes_categories_foreign_keys_cascade.php:18) |
| `category_id`  | FK → `categories` | аналогично → `cascade` |

Создание: [`2023_10_26_233257_create_table_attributes_categories_table.php`](app/Modules/Catalog/Database/Migrations/2023_10_26_233257_create_table_attributes_categories_table.php:14).

### 2.2. Тип атрибута — Value Object `AttributeType`

[`Domain/ValueObjects/AttributeType.php`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:9) инкапсулирует допустимые типы:

| Строковое значение | Старый код | Название  | Метод-предикат |
|--------------------|------------|-----------|----------------|
| `string`  | 101 | Строка   | [`isString()`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:76) |
| `bool`    | 102 | Флажок   | [`isBool()`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:86) |
| `integer` | 103 | Число    | [`isInteger()`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:81) |
| `variant` | 104 | Варианты | [`isVariant()`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:47) |
| `float`   | 105 | Дробное  | [`isFloat()`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:91) |
| `date`    | 106 | Дата     | [`isDate()`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:96) |

Модель [`Infrastructure/Models/Attribute.php`](app/Modules/Catalog/Infrastructure/Models/Attribute.php:61) дублирует те же предикаты (`isVariant()`, `isBool()`, `isNumeric()`, `isString()`, `isDate()`) уже на уровне Eloquent.

### 2.3. Как хранится значение атрибута у товара

Поле `attributes_products.value` — JSON-колонка. Формат зависит от типа атрибута:

- **variant** — массив ID вариантов: `[101, 102]`.
- **string** — строка: `"красный"`.
- **integer** — число: `42`.
- **float** — число с плавающей точкой: `3.14`.
- **bool** — булево: `true`.
- **date** — строка даты.

Чтение значения реализовано в [`Product::Value()`](app/Modules/Catalog/Infrastructure/Models/Product.php:609) — оно декодирует `pivot->value` через `json_decode(..., true)`. Для нового слоя чтение значения вынесено в [`AttributeProductRepository::valueOf()`](app/Modules/Catalog/Infrastructure/Persistence/AttributeProductRepository.php:11), который так же декодирует `value` через `json_decode(..., true)` и возвращает `null`, если связи нет. Запись «живого» примера — в сервисе [`AttachAttributeProductService::attachVariantToProduct()`](app/Modules/Catalog/Application/Services/AttachAttributeProductService.php:138), который пишет `json_encode([variant_id])`.

### 2.4. Кодовая архитектура атрибутов

```
Domain
 ├─ Entities
 │   ├─ AttributeEntity.php        (id, name, type, groupId, multiple, filter, showIn, sameAs, variants[])
 │   └─ AttributeVariantEntity.php (id, attributeId, name, slug)
 ├─ ValueObjects/AttributeType.php
 └─ Interfaces
     ├─ AttributeRepositoryInterface.php
     ├─ AttributeGroupRepositoryInterface.php
     └─ AttributeCategoryRepositoryInterface.php

Application
 ├─ Actions/Attribute/*           (Index/View/List/Create/Update/Remove)
 ├─ Actions/AttributeCategory/*   (List/Assign/Attach/Detach)
 ├─ DTOs/Attribute/*              (Spatie Data: Create/Update/Index/View/Variant/Category/Filter)
 └─ Services
     ├─ AttachAttributeProductService.php
     └─ DimensionsFromAttributeService.php

Infrastructure
 ├─ Models  (Attribute, AttributeVariant, AttributeGroup, AttributeCategory, AttributeProduct)
 └─ Persistence/AttributeRepository.php  (+ Group, Category реализации)

Presentation
 ├─ Presentation/Http/Controllers/Web/AttributeGroupController.php
 └─ (легаси) Controllers/AttributeController.php
```

**Поток нового CRUD атрибута** (Clean Architecture):

1. Маршрут [`Route::resource('attribute', AttributeController::class)`](app/Modules/Catalog/routes/web.php:194).
2. Контроллер вызывает UseCase/Query, например [`IndexAttributeQuery::execute()`](app/Modules/Catalog/Application/Actions/Attribute/IndexAttributeQuery.php:35).
3. UseCase обращается к [`AttributeRepositoryInterface`](app/Modules/Catalog/Domain/Interfaces/AttributeRepositoryInterface.php:12).
4. Реализация [`Infrastructure/Persistence/AttributeRepository.php`](app/Modules/Catalog/Infrastructure/Persistence/AttributeRepository.php:21) читает/пишет Eloquent и маппит модель ↔ [`AttributeEntity`](app/Modules/Catalog/Domain/Entities/AttributeEntity.php:10) через `hydrate()`/`save()`.
5. DTO [`AttributeViewData`](app/Modules/Catalog/Application/DTOs/Attribute/AttributeViewData.php:19) сериализуется в `snake_case` для фронтенда (Vue: `Info.vue`, `VarianField.vue`).

---

## 3. Подсистема «Модификации товаров» (Modification)

Модификация — это **группа товаров-вариантов**, которые отличаются друг от друга только значениями атрибутов типа `variant`. Например, «Диван Милан» в разных цветах/материалах.

После рефакторинга данные модификаций **нормализованы**: JSON-колонки (`attributes_json`, `values_json`) вынесены в отдельные таблицы, а роль базового товара хранится на связи `is_primary`, а не в `base_product_id`.

### 3.1. Модель данных (таблицы)

#### `modifications` — модификация

| Поле              | Тип   | Примечание |
|-------------------|-------|------------|
| `id`              | bigint PK |         |
| `name`            | string, unique | Название |
| `created_at`, `updated_at` | timestamp | |

Создание: [`2023_11_08_182617_create_modifications_table.php`](app/Modules/Catalog/Database/Migrations/2023_11_08_182617_create_modifications_table.php:14); колонки `base_product_id` и `attributes_json` удалены в [`2026_10_06_170300_restructure_modifications_table.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_170300_restructure_modifications_table.php:11).

#### `modification_attributes` — оси модификации (бывший `attributes_json`)

| Поле             | Тип   | Примечание |
|------------------|-------|------------|
| `modification_id` | FK → `modifications` | `onDelete cascade` |
| `attribute_id`    | FK → `attributes` | `onDelete restrict` |
| `sort`            | unsignedSmallInteger, default 0 | порядок осей |

Составной PK `(modification_id, attribute_id)`, индекс `(modification_id, sort)`.

Создание: [`2026_10_06_170200_create_modification_attributes_table.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_170200_create_modification_attributes_table.php:13).

#### `modifications_products` — товары внутри модификации (pivot)

| Поле             | Тип   | Примечание |
|------------------|-------|------------|
| `id`             | bigint PK | суррогатный ключ (нужен для значений ниже) |
| `modification_id` | FK → `modifications` | `onDelete cascade` |
| `product_id`      | FK → `products` | `onDelete cascade` |
| `is_primary`      | bool, default false | базовый товар |
| `created_at`, `updated_at` | timestamp | |

Уникальность `(modification_id, product_id)`.

Создание: [`2023_11_08_182634_create_modifications_products_table.php`](app/Modules/Catalog/Database/Migrations/2023_11_08_182634_create_modifications_products_table.php:14); реструктуризация (суррогатный `id`, `is_primary`, удаление `values_json`) — [`2026_10_06_170000_restructure_modifications_products_table.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_170000_restructure_modifications_products_table.php:13) и [`2026_10_06_170100_create_modification_product_values_table.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_170100_create_modification_product_values_table.php:13).

#### `modification_product_values` — значения товара в модификации (бывший `values_json`)

| Поле             | Тип   | Примечание |
|------------------|-------|------------|
| `modification_product_id` | FK → `modifications_products` | `onDelete cascade` |
| `attribute_id`    | FK → `attributes` | `onDelete restrict` |
| `variant_id`      | FK → `attribute_variants` | `onDelete restrict` |

Составной PK `(modification_product_id, attribute_id)`, индекс `(attribute_id, variant_id)` — для фильтра «все товары с цветом=белый».

Создание: [`2026_10_06_170100_create_modification_product_values_table.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_170100_create_modification_product_values_table.php:13).

### 3.2. Domain-сущности

- [`ModificationEntity`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:12) — aggregate root: `id` (nullable), `name`, `attributes` (массив ID осей), `products` (массив [`ModificationProductEntity`](app/Modules/Catalog/Domain/Entities/ModificationProductEntity.php:12), ключ — `productId`). Методы: [`create()`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:64), [`rename()`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:69), [`addProduct()`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:77), [`removeProduct()`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:103), [`setPrimary()`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:119), [`primaryProductId()`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:129), [`withId()`](app/Modules/Catalog/Domain/Entities/ModificationEntity.php:140). Роль базового товара — на связи `is_primary`, в агрегате нет `base_product_id`.
- [`ModificationProductEntity`](app/Modules/Catalog/Domain/Entities/ModificationProductEntity.php:12) — связь «товар → значения»: `productId`, `values` (`attribute_id => variant_id`), `isPrimary`; методы `makePrimary()`, `demote()`.
- [`ModificationAttributes`](app/Modules/Catalog/Domain/ValueObjects/ModificationAttributes.php:25) — коллекция ID осей: 1..`MAX` (=3), без дубликатов, порядок сохраняется.
- [`ModificationValues`](app/Modules/Catalog/Domain/ValueObjects/ModificationValues.php:7) — карта `attribute_id => variant_id`.

### 3.3. Кодовая архитектура модификаций

```
Domain
 ├─ Entities
 │   ├─ ModificationEntity.php        (aggregate root)
 │   └─ ModificationProductEntity.php (связь товара с модификацией)
 ├─ ValueObjects
 │   ├─ ModificationAttributes.php
 │   └─ ModificationValues.php
 └─ Interfaces/ModificationRepositoryInterface.php

Application
 ├─ Actions/Modification
 │   ├─ CreateModificationUseCase.php
 │   ├─ IndexModificationQuery.php
 │   └─ SearchModificationCreateQuery.php
 ├─ Actions/Product/SearchProductQuery.php
 ├─ DTOs/Modification
 │   ├─ ModificationCreateData.php
 │   ├─ ModificationIndexData.php
 │   └─ ModificationCreateSearchData.php
 ├─ DTOs/Product/ProductSearchData.php
 └─ Services/ModificationValuesResolver.php

Infrastructure
 ├─ Models
 │   ├─ Modification.php              (modifications)
 │   ├─ ModificationProduct.php       (modifications_products)
 │   ├─ ModificationAttribute.php     (modification_attributes)
 │   └─ ModificationProductValue.php  (modification_product_values)
 └─ Persistence
     ├─ ModificationRepository.php    (save/getById/delete/findAll/getUsedProductIds)
     └─ AttributeProductRepository.php (valueOf)

Presentation
 └─ Presentation/Http/Controllers/Web/ModificationController.php
```

**Репозиторий** [`ModificationRepository`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:16) сохраняет агрегат целиком одной командой [`save()`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:25):

1. создаёт/обновляет строку `modifications` (`name`);
2. [`syncAttributes()`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:56) — пересоздаёт оси в `modification_attributes` (с `sort`);
3. [`syncProducts()`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:73) — пересоздаёт связи в `modifications_products` (с `is_primary`);
4. [`syncValues()`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:108) — пересоздаёт значения в `modification_product_values`.

Чтение — [`hydrate()`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:126) собирает агрегат обратно. Список — [`findAll()`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:47) возвращает пагинатор `ModificationIndexData`.

**Основные сценарии (Clean Architecture):**

| Action | Назначение |
|--------|------------|
| [`CreateModificationUseCase`](app/Modules/Catalog/Application/Actions/Modification/CreateModificationUseCase.php:20) | Создание: право `catalog.modification.create`, проверка товара, резолв значений первого товара, `addProduct(primary: true)`, `save()` в транзакции |
| [`IndexModificationQuery`](app/Modules/Catalog/Application/Actions/Modification/IndexModificationQuery.php:14) | Список с пагинацией (`findAll`) |
| [`SearchModificationCreateQuery`](app/Modules/Catalog/Application/Actions/Modification/SearchModificationCreateQuery.php:16) | `searchCreate`: поиск товаров **без уже занятых** + атрибуты-варианты найденных товаров |

**Формирование значений первого товара** — в [`ModificationValuesResolver::forProduct()`](app/Modules/Catalog/Application/Services/ModificationValuesResolver.php:16): для каждой оси берётся значение из `attributes_products` через [`AttributeProductRepository::valueOf()`](app/Modules/Catalog/Infrastructure/Persistence/AttributeProductRepository.php:11) (декодированный JSON); для `variant` берётся первый ID.

### 3.4. Связи со стороны товара (легаси, требуют переноса)

В [`Product`](app/Modules/Catalog/Infrastructure/Models/Product.php:830) объявлены связи, рассчитанные на старую схему (с `base_product_id`/`values_json`). После рефакторинга они устарели и подлежат замене на связи через `modifications_products.is_primary` и `modification_product_values`:

- [`main_modification()`](app/Modules/Catalog/Infrastructure/Models/Product.php:830) — `hasOne(Modification, 'base_product_id')` (товар — базовый) → заменить на связь через pivot с `is_primary = true`.
- [`modification_product()`](app/Modules/Catalog/Infrastructure/Models/Product.php:835) — `hasOne(ModificationProduct, 'product_id')` (товар — участник).
- [`modification()`](app/Modules/Catalog/Infrastructure/Models/Product.php:840) — `hasOneThrough` сквозь pivot.
- [`AttributeIsModification()`](app/Modules/Catalog/Infrastructure/Models/Product.php:946) — проверяет, участвует ли атрибут в модификации товара.

---

## 4. Маршруты (entry points)

Из [`routes/web.php`](app/Modules/Catalog/routes/web.php:32):

- `attribute` — REST CRUD ([`Route::resource('attribute', ...)`](app/Modules/Catalog/routes/web.php:194)).
- `attribute-group` — REST CRUD ([`Route::resource('attribute-group', ...)`](app/Modules/Catalog/routes/web.php:47)).
- `modification` — REST CRUD + кастомные ([`Route::resource('modification', ...)`](app/Modules/Catalog/routes/web.php:197)):
  - `POST /set-base/{modification}` — смена базового (легаси).
  - `POST /search` — поиск (легаси).
  - `POST /search-create` — `searchCreate` для диалога создания (Clean Architecture).
  - `POST /rename/{modification}` — переименование (легаси).
  - `POST /add-product/{modification}` — добавить товар (легаси).
  - `DELETE /del-product/{modification}` — удалить товар (легаси).
- На уровне товара:
  - `POST /product/search` — поиск товара → [`ProductController::search()`](app/Modules/Catalog/Controllers/ProductController.php:185), через [`SearchProductQuery`](app/Modules/Catalog/Application/Actions/Product/SearchProductQuery.php:14).
  - `POST /product/attr-modification/{product}` ([`routes/web.php:210`](app/Modules/Catalog/routes/web.php:210)) — легаси.
  - `POST /product/attribute/{product}` — редактирование атрибутов товара ([`routes/web.php:237`](app/Modules/Catalog/routes/web.php:237)).

---

## 5. Примеры JSON: как выглядят данные

### 5.1. Группа атрибутов (`attribute_groups`)

```json
{ "id": 1, "name": "Основные характеристики", "sort": 1, "svg": null }
```

### 5.2. Атрибуты (`attributes`)

```json
{ "id": 10, "group_id": 1, "name": "Цвет",     "type": "variant", "multiple": true,  "filter": true,  "show_in": true, "sameAs": "", "sort": 0, "widget": "" }
{ "id": 11, "group_id": 1, "name": "Материал", "type": "variant", "multiple": true,  "filter": true,  "show_in": true, "sameAs": "", "sort": 1, "widget": "" }
{ "id": 12, "group_id": 2, "name": "Высота",   "type": "integer", "multiple": false, "filter": false, "show_in": true, "sameAs": "", "sort": 0, "widget": "" }
```

### 5.3. Варианты (`attribute_variants`)

```json
{ "id": 101, "attribute_id": 10, "name": "Белый",  "slug": "belyj" }
{ "id": 102, "attribute_id": 10, "name": "Черный", "slug": "chernyj" }
{ "id": 201, "attribute_id": 11, "name": "Велюр",  "slug": "veljur" }
{ "id": 202, "attribute_id": 11, "name": "Кожа",   "slug": "kozha" }
```

### 5.4. Значение атрибута у товара (`attributes_products`)

```json
{ "attribute_id": 10, "product_id": 500, "value": [101, 102] }
{ "attribute_id": 12, "product_id": 500, "value": 42 }
```

> Для `variant` — массив ID вариантов; для скалярных типов — само значение (строка/число/булево/дата).

### 5.5. Привязка атрибута к категории (`attributes_categories`)

```json
{ "attribute_id": 10, "category_id": 7 }
```

### 5.6. Модификация (`modifications`) и её оси (`modification_attributes`)

```json
{ "id": 1, "name": "Диван Милан" }
```

```json
{ "modification_id": 1, "attribute_id": 10, "sort": 0 }
{ "modification_id": 1, "attribute_id": 11, "sort": 1 }
```

То есть модификация «Диван Милан» задана осями «Цвет» (id=10) и «Материал» (id=11).

### 5.7. Товар внутри модификации (`modifications_products` + `modification_product_values`)

```json
{ "id": 901, "modification_id": 1, "product_id": 500, "is_primary": true }
{ "id": 902, "modification_id": 1, "product_id": 501, "is_primary": false }
{ "id": 903, "modification_id": 1, "product_id": 502, "is_primary": false }
```

```json
{ "modification_product_id": 901, "attribute_id": 10, "variant_id": 101 }
{ "modification_product_id": 901, "attribute_id": 11, "variant_id": 201 }
{ "modification_product_id": 902, "attribute_id": 10, "variant_id": 102 }
{ "modification_product_id": 902, "attribute_id": 11, "variant_id": 202 }
```

Товар `501` = «Цвет: Белый (101) + Материал: Велюр (201)», товар `502` = «Черный + Кожа». Базовый — товар `500` (`is_primary = true`).

### 5.8. Ответ списка модификаций (`ModificationIndexData`)

Формируется в [`IndexModificationQuery`](app/Modules/Catalog/Application/Actions/Modification/IndexModificationQuery.php:14) через [`ModificationRepository::findAll()`](app/Modules/Catalog/Infrastructure/Persistence/ModificationRepository.php:47):

```json
{
  "id": 1,
  "name": "Диван Милан",
  "quantity": 3,
  "primary_product_id": 500,
  "name_attributes": ["Цвет", "Материал"]
}
```

Изображение базового товара на фронтенде подгружается отдельно по `primary_product_id` через `admin.photo.get-by-ids` в [`Index.vue`](resources/js/Pages/Catalog/Modification/Index.vue:118).

### 5.9. Ответ карточки атрибута (Clean Architecture, `AttributeViewData`)

Сериализация DTO [`AttributeViewData`](app/Modules/Catalog/Application/DTOs/Attribute/AttributeViewData.php:19):

```json
{
  "id": 10,
  "name": "Цвет",
  "type": "variant",
  "type_text": "Варианты",
  "is_variant": true,
  "group_id": 1,
  "group": "Основные характеристики",
  "multiple": true,
  "filter": true,
  "show_in": true,
  "sameAs": null,
  "categories": [ { "id": 7, "name": "Диваны" } ],
  "variants": [
    { "id": 101, "name": "Белый",  "slug": "belyj" },
    { "id": 102, "name": "Черный", "slug": "chernyj" }
  ]
}
```

---

## 6. Диаграмма связей

```
attribute_groups 1 ────< attributes 1 ────< attribute_variants
                            │
                            │ (pivot attributes_categories)
                            ├────────> categories
                            │
                            │ (pivot attributes_products)
                            └────────> products

modifications 1 ────< modification_attributes >──── attributes
       │
       └──────< modifications_products >──────────── products
                   │ (is_primary)
                   └──────< modification_product_values >──── attribute_variants
                               (attribute_id => variant_id)
```

---

## 7. Что важно учесть при анализе и рефакторинге

1. **Дублирование слоёв атрибутов.** Новый CRUD (Clean Architecture) и легаси-репозиторий ([`Repository/AttributeRepository.php`](app/Modules/Catalog/Repository/AttributeRepository.php:13)) работают с одной и той же таблицей `attributes`. Нужно следить, чтобы правки не расходились.
2. **Модификации — переходное состояние.** Создание/индекс/поиск уже на Clean Architecture (`Domain/Entities/ModificationEntity`, `Application/Actions/Modification/*`, `Infrastructure/Persistence/ModificationRepository`). Карточка (`ModificationWithToArray`) и операции `rename/delete/set-base/add-product/del-product` ещё в легаси-слое — их предстоит перенести.
3. **Хранение значений.** Значения атрибутов товара (`attributes_products.value`) по-прежнему в JSON. Состав модификаций **нормализован**: `modification_attributes` (оси) и `modification_product_values` (значения) вместо `attributes_json`/`values_json`.
4. **Смена типа `attributes.type`.** Было `integer` (101–106), стало `string`. Исторические данные мигрированы [`2026_10_06_160000_update_attributes_table_type_to_string.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_160000_update_attributes_table_type_to_string.php:20).
5. **Ограничение модификаций.** Максимум 3 оси (`ModificationAttributes::MAX`), валидация в [`ModificationCreateData`](app/Modules/Catalog/Application/DTOs/Modification/ModificationCreateData.php:27) и блокировка кнопки на фронтенде.
6. **Базовый товар** — связь `modifications_products.is_primary = true`, а не колонка `base_product_id`. Миграция переносит `base_product_id` → `is_primary`.
7. **Фото через `Photo`.** Изображения атрибутов/вариантов/товаров отдаются через `GetPhotoStatic` и модель `Photo` с `model_type` `catalog.attribute`, `catalog.attribute-variant`, `catalog.product` (см. [`AttributeRepository::deleteVariantPhotos()`](app/Modules/Catalog/Infrastructure/Persistence/AttributeRepository.php:224)). В новых DTO списка изображение не зашивается — фронтенд подгружает его по `primary_product_id` через `admin.photo.get-by-ids`.
