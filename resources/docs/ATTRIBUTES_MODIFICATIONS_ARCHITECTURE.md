# Архитектура системы атрибутов (Attribute) и модификаций (Modification)

> Модуль: `App\Modules\Catalog`
> Цель документа — дать целостное представление о том, как устроены «атрибуты» и «модификации товаров», какие слои кода задействованы, как хранятся данные в БД и в каком виде они отдаются на фронтенд.

---

## 1. Общая картина: два стиля архитектуры в одном модуле

Модуль `Catalog` исторически развивался, поэтому внутри него **сосуществуют два подхода**:

### 1.1. Новая «Clean Architecture» (Domain / Application / Infrastructure)

Используется для **CRUD атрибутов** и их привязки к категориям. Классические слои:

- **Domain** — [`Domain/Entities/AttributeEntity.php`](app/Modules/Catalog/Domain/Entities/AttributeEntity.php:10), [`Domain/Entities/AttributeVariantEntity.php`](app/Modules/Catalog/Domain/Entities/AttributeVariantEntity.php:7), [`Domain/ValueObjects/AttributeType.php`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:9), интерфейсы репозиториев в [`Domain/Interfaces/`](app/Modules/Catalog/Domain/Interfaces/AttributeRepositoryInterface.php:12).
- **Application** — сценарии (`Actions/Attribute/*`, `Actions/AttributeCategory/*`), DTO на базе `Spatie\LaravelData` (`DTOs/Attribute/*`), сервисы (`Application/Services/*`).
- **Infrastructure** — реализация репозиториев ([`Infrastructure/Persistence/AttributeRepository.php`](app/Modules/Catalog/Infrastructure/Persistence/AttributeRepository.php:21)) поверх Eloquent-моделей [`Infrastructure/Models/Attribute.php`](app/Modules/Catalog/Infrastructure/Models/Attribute.php:26).

### 1.2. «Легаси»-слой (прямые Eloquent-модели + Repository + Service)

Используется для **модификаций** и части старых эндпоинтов атрибутов. Здесь нет отдельных Domain-сущностей — Eloquent-модель является одновременно и «сущностью»:

- [`Entity/Modification.php`](app/Modules/Catalog/Entity/Modification.php:23) — Eloquent-модель `modifications`.
- [`Entity/ModificationProduct.php`](app/Modules/Catalog/Entity/ModificationProduct.php:14) — pivot-модель `modifications_products`.
- [`Repository/ModificationRepository.php`](app/Modules/Catalog/Repository/ModificationRepository.php:15) — чтение/сериализация для списка и карточки.
- [`Service/ModificationService.php`](app/Modules/Catalog/Service/ModificationService.php:11) — бизнес-логика (создание, добавление товара, смена базового товара).
- [`Repository/AttributeRepository.php`](app/Modules/Catalog/Repository/AttributeRepository.php:13) — «старый» репозиторий атрибутов (списки, поиск).
- Контроллеры: [`Controllers/AttributeController.php`](app/Modules/Catalog/Controllers/AttributeController.php), [`Controllers/ModificationController.php`](app/Modules/Catalog/Controllers/ModificationController.php).

> **Ключевой вывод для анализа:** атрибуты продублированы в двух мирах (новый CRUD через Clean Architecture + старый репозиторий/сервисы), а модификации существуют **только** в легаси-слое. При рефакторинге модификаций их придётся «достроить» до Clean Architecture по образцу атрибутов.

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

Чтение значения реализовано в [`Product::Value()`](app/Modules/Catalog/Infrastructure/Models/Product.php:609) — оно декодирует `pivot->value` через `json_decode(..., true)`. Само значение доступно через `Attribute::Value()` ([`Attribute::Value()`](app/Modules/Catalog/Infrastructure/Models/Attribute.php:148)). Запись «живого» примера — в сервисе [`AttachAttributeProductService::attachVariantToProduct()`](app/Modules/Catalog/Application/Services/AttachAttributeProductService.php:138), который пишет `json_encode([variant_id])`.

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

### 3.1. Модель данных (таблицы)

#### `modifications` — модификация

| Поле              | Тип   | Примечание |
|-------------------|-------|------------|
| `id`              | bigint PK |         |
| `name`            | string, unique | Название (регистрация добавляет случайный суффикс, если имя занято) |
| `base_product_id` | FK → `products` | Базовый товар, было `restrict`, стало `cascade` в [`2024_06_05_185214_update_modifications_table.php`](app/Modules/Catalog/Database/Migrations/2024_06_05_185214_update_modifications_table.php:19) |
| `attributes_json`  | json   | JSON-массив ID атрибутов-вариантов, задающих модификацию |

Создание: [`2023_11_08_182617_create_modifications_table.php`](app/Modules/Catalog/Database/Migrations/2023_11_08_182617_create_modifications_table.php:14).

#### `modifications_products` — товары внутри модификации (pivot)

| Поле             | Тип   | Примечание |
|------------------|-------|------------|
| `modification_id` | FK → `modifications` | было `restrict`, стало `cascade` в [`2024_06_05_185441_update_modifications_products_table.php`](app/Modules/Catalog/Database/Migrations/2024_06_05_185441_update_modifications_products_table.php:19) |
| `product_id`      | FK → `products` | было `restrict`, стало `cascade` в [`2024_06_05_184118_update_modifications_products_table.php`](app/Modules/Catalog/Database/Migrations/2024_06_05_184118_update_modifications_products_table.php:19) |
| `values_json`     | json  | Карта `{attribute_id: variant_id}` для конкретного товара |

Создание: [`2023_11_08_182634_create_modifications_products_table.php`](app/Modules/Catalog/Database/Migrations/2023_11_08_182634_create_modifications_products_table.php:14).

### 3.2. Модель `Modification`

[`Entity/Modification.php`](app/Modules/Catalog/Entity/Modification.php:23) — Eloquent-модель, но с «богатой» логикой:

- Свойство-кэш [`$prod_attributes`](app/Modules/Catalog/Entity/Modification.php:27) содержит массив моделей `Attribute`.
- [`register()`](app/Modules/Catalog/Entity/Modification.php:34) — фабрика: проверяет, что **все** атрибуты имеют тип `variant` ([`AttributeType::TYPE_VARIANT`](app/Modules/Catalog/Domain/ValueObjects/AttributeType.php:14)), при неуникальном имени добавляет случайный суффикс.
- Хук [`saving`](app/Modules/Catalog/Entity/Modification.php:78) сериализует `prod_attributes` → `attributes_json` (массив ID).
- Хук [`retrieved`](app/Modules/Catalog/Entity/Modification.php:86) гидратирует `prod_attributes` обратно из `attributes_json`.
- [`products()`](app/Modules/Catalog/Entity/Modification.php:60) — `belongsToMany(Product, 'modifications_products')` с `withPivot('values_json')`.
- [`getVariations()`](app/Modules/Catalog/Entity/Modification.php:93) — строит декартово произведение вариантов для 1–3 атрибутов; при `> 3` бросает `DomainException`.
- [`productByVariant()`](app/Modules/Catalog/Entity/Modification.php:66) — ищет товар по карте `{attribute_id => variant_id}`.

### 3.3. Кодовая архитектура модификаций

```
Entity
 ├─ Modification.php        (Eloquent + логика)
 └─ ModificationProduct.php (pivot + register() + values[])

Repository/ModificationRepository.php   (getIndex, ModificationToArray, ModificationWithToArray)
Service/ModificationService.php         (create, rename, delete, addProduct, delProduct, setBase)
Controllers/ModificationController.php  (REST + custom actions)
```

**Основные операции** ([`ModificationService`](app/Modules/Catalog/Service/ModificationService.php:11)):

| Метод | Назначение |
|-------|------------|
| [`create()`](app/Modules/Catalog/Service/ModificationService.php:20) | Создание модификации + привязка базового товара |
| [`rename()`](app/Modules/Catalog/Service/ModificationService.php:35) | Переименование |
| [`delete()`](app/Modules/Catalog/Service/ModificationService.php:41) | Отвязка товаров и удаление |
| [`addProduct()`](app/Modules/Catalog/Service/ModificationService.php:47) | Добавить товар-вариант |
| [`delProduct()`](app/Modules/Catalog/Service/ModificationService.php:69) | Удалить товар-вариант |
| [`setBase()`](app/Modules/Catalog/Service/ModificationService.php:75) | Смена базового товара (перенос парсера, фото, эквивалента, групп) |

**Формирование `values_json`** происходит в [`attachProduct()`](app/Modules/Catalog/Service/ModificationService.php:54): для каждого атрибута модификации берётся значение из товара через `Product::Value()`, и если это массив — берётся первый элемент `(int)$value[0]`, иначе значение как есть. Итог: `{attribute_id: variant_id}`.

### 3.4. Связи со стороны товара

В [`Product`](app/Modules/Catalog/Infrastructure/Models/Product.php:830) объявлены:

- [`main_modification()`](app/Modules/Catalog/Infrastructure/Models/Product.php:830) — `hasOne(Modification, 'base_product_id')` (товар — базовый).
- [`modification_product()`](app/Modules/Catalog/Infrastructure/Models/Product.php:835) — `hasOne(ModificationProduct, 'product_id')` (товар — участник).
- [`modification()`](app/Modules/Catalog/Infrastructure/Models/Product.php:840) — `hasOneThrough` сквозь pivot.
- [`AttributeIsModification()`](app/Modules/Catalog/Infrastructure/Models/Product.php:946) — проверяет, участвует ли атрибут в модификации товара.

---

## 4. Маршруты (entry points)

Из [`routes/web.php`](app/Modules/Catalog/routes/web.php:32):

- `attribute` — REST CRUD ([`Route::resource('attribute', ...)`](app/Modules/Catalog/routes/web.php:194)).
- `attribute-group` — REST CRUD ([`Route::resource('attribute-group', ...)`](app/Modules/Catalog/routes/web.php:47)).
- `modification` — REST CRUD + кастомные ([`Route::resource('modification', ...)`](app/Modules/Catalog/routes/web.php:197)):
  - `POST /set-base/{modification}` — смена базового.
  - `POST /search` — поиск.
  - `POST /rename/{modification}` — переименование.
  - `POST /add-product/{modification}` — добавить товар.
  - `DELETE /del-product/{modification}` — удалить товар.
- На уровне товара:
  - `POST /product/attr-modification/{product}` ([`routes/web.php:210`](app/Modules/Catalog/routes/web.php:210)).
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

### 5.6. Модификация (`modifications`)

```json
{ "id": 1, "name": "Диван Милан", "base_product_id": 500, "attributes_json": [10, 11] }
```

Здесь `attributes_json` = `[10, 11]` означает, что варианты модификации различаются по атрибутам «Цвет» (id=10) и «Материал» (id=11).

### 5.7. Товар внутри модификации (`modifications_products`)

```json
{ "modification_id": 1, "product_id": 501, "values_json": { "10": 101, "11": 201 } }
{ "modification_id": 1, "product_id": 502, "values_json": { "10": 102, "11": 202 } }
```

То есть товар `501` = «Цвет: Белый (101) + Материал: Велюр (201)», товар `502` = «Черный + Кожа».

### 5.8. Ответ карточки модификации (`ModificationWithToArray`)

Формируется в [`ModificationRepository::ModificationWithToArray()`](app/Modules/Catalog/Repository/ModificationRepository.php:51):

```json
{
  "id": 1,
  "name": "Диван Милан",
  "base_product_id": 500,
  "attributes_json": [10, 11],
  "quantity": 2,
  "name_attributes": ["Цвет", "Материал"],
  "image": "https://.../catalog/product/500/mini.jpg",
  "base_product": { "id": 500, "name": "Диван Милан", "...": "..." },
  "attributes": [
    {
      "id": 10,
      "name": "Цвет",
      "image": "https://.../catalog/attribute/10.jpg",
      "variants": [
        { "id": 101, "name": "Белый",  "image": "https://.../catalog/attribute-variant/101.jpg" },
        { "id": 102, "name": "Черный", "image": "https://.../catalog/attribute-variant/102.jpg" }
      ]
    },
    {
      "id": 11,
      "name": "Материал",
      "image": "https://.../catalog/attribute/11.jpg",
      "variants": [
        { "id": 201, "name": "Велюр", "image": "https://.../catalog/attribute-variant/201.jpg" },
        { "id": 202, "name": "Кожа",  "image": "https://.../catalog/attribute-variant/202.jpg" }
      ]
    }
  ],
  "products": [
    { "id": 501, "name": "Диван Милан (белый/велюр)", "image": "https://.../mini.jpg", "variants": ["Белый", "Велюр"] },
    { "id": 502, "name": "Диван Милан (черный/кожа)",  "image": "https://.../mini.jpg", "variants": ["Черный", "Кожа"] }
  ]
}
```

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

products 1 ────< modifications (base_product_id)
modifications 1 ────< modifications_products >──── products
                         (values_json: {attribute_id: variant_id})
```

---

## 7. Что важно учесть при анализе и рефакторинге

1. **Дублирование слоёв атрибутов.** Новый CRUD (Clean Architecture) и легаси-репозиторий ([`Repository/AttributeRepository.php`](app/Modules/Catalog/Repository/AttributeRepository.php:13)) работают с одной и той же таблицей `attributes`. Нужно следить, чтобы правки не расходились.
2. **Модификации — только легаси.** В `Domain` и `Application` нет `ModificationEntity`/UseCase. Перенос на Clean Architecture — отдельная задача.
3. **Хранение значений в JSON.** Значения атрибутов и состав модификаций хранятся в JSON-колонках (`value`, `attributes_json`, `values_json`). Это гибко, но исключает индексы/ограничения целостности на значениях.
4. **Смена типа `attributes.type`.** Было `integer` (101–106), стало `string`. Исторические данные мигрированы [`2026_10_06_160000_update_attributes_table_type_to_string.php`](app/Modules/Catalog/Database/Migrations/2026_10_06_160000_update_attributes_table_type_to_string.php:20).
5. **Ограничение модификаций.** Максимум 3 атрибута-варианта на одну модификацию (жёстко в [`getVariations()`](app/Modules/Catalog/Entity/Modification.php:137)).
6. **Фото через `Photo`.** Изображения атрибутов/вариантов/товаров отдаются через `GetPhotoStatic` и модель `Photo` с `model_type` `catalog.attribute`, `catalog.attribute-variant`, `catalog.product` (см. [`AttributeRepository::deleteVariantPhotos()`](app/Modules/Catalog/Infrastructure/Persistence/AttributeRepository.php:224)).
