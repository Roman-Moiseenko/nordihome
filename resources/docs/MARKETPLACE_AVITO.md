# Модуль Marketplace — Авито (инструкция для ИИ-агента)

> Документ описывает текущее состояние модуля [`app/Modules/Marketplace`](../../app/Modules/Marketplace)
> и алгоритмы работы с Авито. Перед внесением изменений обязательно прочитай
> [`INSTRUCTION.md`](INSTRUCTION.md) (общие правила Clean Architecture) и
> [`CATALOG_CRUD.md`](CATALOG_CRUD.md) (паттерны страниц Index/Show).

---

## 1. Что уже реализовано

Модуль построен по Clean Architecture (Domain / Application / Infrastructure / Presentation).
Все действия — через UseCase/Query, контроллеры тонкие, данные передаются через DTO
(Spatie Laravel Data). Репозитории работают с сущностями (`*Entity`), модели Eloquent —
только в Infrastructure.

### 1.1 Сущности и таблицы

| Таблица | Модель | Сущность | Назначение |
|---------|--------|----------|------------|
| `avito_categories` | [`AvitoCategory`](../../app/Modules/Marketplace/Infrastructure/Models/AvitoCategory.php) | [`AvitoCategoryEntity`](../../app/Modules/Marketplace/Domain/Entities/AvitoCategoryEntity.php) | Дерево категорий Авито (NestedSet, сверка по `slug`) |
| `avito_category_fields` | [`AvitoCategoryField`](../../app/Modules/Marketplace/Infrastructure/Models/AvitoCategoryField.php) | [`AvitoCategoryFieldEntity`](../../app/Modules/Marketplace/Domain/Entities/AvitoCategoryFieldEntity.php) | Параметры категории (описание из xlsx) |
| `avito_products` | [`AvitoProduct`](../../app/Modules/Marketplace/Infrastructure/Models/AvitoProduct.php) | [`AvitoProductEntity`](../../app/Modules/Marketplace/Domain/Entities/AvitoProductEntity.php) | Товары/объявления Авито |
| `avito_parameters` | [`AvitoParameter`](../../app/Modules/Marketplace/Infrastructure/Models/AvitoParameter.php) | [`AvitoParameterEntity`](../../app/Modules/Marketplace/Domain/Entities/AvitoParameterEntity.php) | Общие параметры («Способ размещения», «Состояние», …) |

Миграции лежат в [`Database/Migrations`](../../app/Modules/Marketplace/Database/Migrations).
**Новые миграции не создавались — правились существующие** (они ещё не запущены в БД).

### 1.2 Поля AvitoProduct

| Свойство сущности | Колонка таблицы | Комментарий |
|-------------------|-----------------|-------------|
| `adId` | `ad_id` | id объявления |
| `adNumber` | `ad_number` | номер объявления на Авито |
| `productId` | `product_id` | связь с товаром каталога |
| `avitoCategoryId` | `avito_category_id` | категория Авито |
| `name`, `description`, `price`, `pricePackage`, `tnVed`, `okpd2` | соответствующие | |
| `delivery` | `delivery` | автодоставка |
| `inStock` | `in_stock` | доступность (boolean) |
| `active` | `active` | активность/архив |

### 1.3 Поля AvitoParameter

`name`, `slug` (генерируется через `Str::slug()`), `value` (string|null),
`variants` (массив строк, json), `is_html` (boolean). Если у параметра есть `variants` —
`is_html` всегда `false`. Заполняются сидером [`AvitoParameterSeeder`](../../app/Modules/Marketplace/Database/Seeders/AvitoParameterSeeder.php).

---

## 2. Загрузка структуры из xlsx (главный алгоритм)

Библиотека: `phpoffice/phpspreadsheet` (уже в `composer.json`).

Точка входа: [`AvitoStructureImportService::import()`](../../app/Modules/Marketplace/Application/Services/Avito/AvitoStructureImportService.php),
запускается через [`ImportAvitoStructureUseCase`](../../app/Modules/Marketplace/Application/Actions/Avito/ImportAvitoStructureUseCase.php)
или отложенно через Job [`ImportAvitoStructureJob`](../../app/Modules/Marketplace/Infrastructure/Jobs/ImportAvitoStructureJob.php).

### 2.1 Структура файла

- **Лист 1** — всегда `Инструкция` — пропускается.
- **Один лист объявлений (шаблон Авито):** `Инструкция`, `Объявления`, скрытый `Справочники`.
- **Много листов (выгрузка «АвитоЗагрузки»):** после `Инструкции` чередуются листы
  объявлений и скрытые листы справочников `Спр-...`.

Определение «один/много»: `count($sheetNames) <= 3` → шаблон (single), иначе multi.

### 2.2 Раскладка листа объявлений

| Строка/ячейка | Содержимое |
|---------------|------------|
| `A1` | полный путь категории через `" - "`, например `Для дома и дачи - Мебель и интерьер - Кровати, диваны и кресла - Диваны` |
| строка 2 | названия параметров (по колонкам) |
| строка 3 | обязательность |
| строка 4 | тип параметра |
| строка 5+ | данные товаров (не используются при импорте структуры) |

### 2.3 Категории

Путь из A1 разбивается по `" - "`. Цепочка ищется/создаётся методом
[`AvitoCategoryRepository::findOrCreateByNames()`](../../app/Modules/Marketplace/Infrastructure/Persistence/AvitoCategoryRepository.php)
(по `name` внутри `parent_id`; slug — `Str::slug(name)`, при конфликте добавляется суффикс).
В конце импорта вызывается `fixTree()` для NestedSet.

### 2.4 Пропуск колонок

Колонка пропускается, если:
- название есть в [`AvitoProductFieldMap::MAP`](../../app/Modules/Marketplace/Application/Mappers/AvitoProductFieldMap.php) — это общее поле товара, не параметр категории;
- название есть в [`AvitoProductFieldMap::SKIP`](../../app/Modules/Marketplace/Application/Mappers/AvitoProductFieldMap.php) — поля без соответствия в сущности, которые не должны стать параметрами (`Категория`, `Широта`, `Долгота`, …).

> ⚠️ `MAP` и `SKIP` заполняются вручную. При появлении новых колонок в шаблоне
> дополняй эти массивы. Маппинг двусторонний: `fieldByLabel()` / `labelByField()`.

Всё остальное — параметр категории: `name` → `name`, `tag = Str::slug(name)`,
`is_required` из строки 3, `type` из строки 4, варианты — в `allowed_values`.

### 2.5 Обязательность (строка 3)

| Значение | Результат |
|----------|-----------|
| `Обязательный` | `true` |
| `Станет обязательным <дата>` | `true` |
| `Необязательный` | `false` |
| `Может быть обязательным` | `false` |
| пусто | `false` |

### 2.6 Тип параметра (строка 4)

| Значение | `type` |
|----------|--------|
| `Подробнее о параметре` | `null` (уточняется вручную в UI) |
| `Текст` | `string` |
| `Число` | `integer` |
| `Дробное число` | `float` |
| `Одно значение из выпадающего списка в ячейке` | `string` (+ варианты) |
| `Одно или несколько значений из выпадающего списка в ячейке` | `array` (+ варианты) |
| `Одно значение из листа "..."` | `string` (**без** вариантов — большие справочники) |

### 2.7 Варианты

Варианты подтягиваются только для двух «выпадающих» типов (п. 2.6).

- **Шаблон (single):** из листа `Справочники`, тот же номер колонки, строки со 2-й.
- **Выгрузка (multi):** через `$sheet->getDataValidationCollection()`, формула
  `getFormula1()` вида `'Лист'!$D$2:$D$4` → читается диапазон на скрытом листе.

### 2.8 Сохранение параметров

[`AvitoCategoryFieldRepository::upsertForCategory()`](../../app/Modules/Marketplace/Infrastructure/Persistence/AvitoCategoryFieldRepository.php)
— добавляет/обновляет по паре `(avito_category_id, tag)`, варианты объединяются без дублей.

---

## 3. Backend: контроллер, маршруты, биндинги

Контроллер: [`AvitoController`](../../app/Modules/Marketplace/Presentation/Http/Controllers/Web/AvitoController.php).

Маршруты (префикс `admin/marketplace/avito`, имя `admin.marketplace.avito.`):

| Имя | Метод | Метод контроллера |
|-----|-------|-------------------|
| `index` | GET `/` | `index` (таблица товаров + фильтры) |
| `categories` | GET `/categories` | `categories` (дерево + параметры) |
| `settings` | GET `/settings` | `settings` (AvitoParameter) |
| `settings.update` | POST `/settings` | `updateSettings` |
| `operations` | GET `/operations` | `operations` (заглушка) |
| `import` | POST `/import` | `uploadStructure` (xlsx) |
| `category.fields` | GET `/category/{id}/fields` | `categoryFields` |
| `category.field.type` | POST `/category/{id}/fields/{fieldId}/type` | `updateFieldType` |
| `product.show` | GET `/product/{id}` | `productShow` (заглушка) |
| `product.store` | POST `/product` | `store` (из `SearchAddProduct`) |
| `product.delivery` | POST `/product/{id}/delivery` | `toggleDelivery` |
| `product.active` | POST `/product/{id}/active` | `setActive` |
| `sync-categories` | POST `/sync-categories` | `syncCategories` (legacy API) |
| `sync-fields` | POST `/sync-fields` | `syncFields` (legacy API) |

> `sync-categories`/`sync-fields` — старый API-синк, фактически заменён xlsx-импортом.

Биндинги репозиториев — в
[`MarketplaceServiceProvider::register()`](../../app/Modules/Marketplace/Providers/MarketplaceServiceProvider.php);
там же регистрируются сидеры (`MarketplaceRoleSeeder`, `AvitoParameterSeeder`).

Права: `marketplace.avito.{create,edit,view,delete}` (см.
[`MarketplaceRoleSeeder`](../../app/Modules/Marketplace/Database/Seeders/MarketplaceRoleSeeder.php)).

---

## 4. Frontend

Страницы в [`resources/js/Pages/Marketplace/Avito`](../../resources/js/Pages/Marketplace/Avito):

| Файл | Назначение |
|------|------------|
| [`Index.vue`](../../resources/js/Pages/Marketplace/Avito/Index.vue) | список товаров, кнопки подразделов, `SearchAddProduct`, фильтры (`show`, `delivery`, `search`), таблица с действиями |
| [`Categories.vue`](../../resources/js/Pages/Marketplace/Avito/Categories.vue) | загрузка xlsx + две колонки: дерево категорий и параметры листовой категории |
| [`Settings.vue`](../../resources/js/Pages/Marketplace/Avito/Settings.vue) | заполнение `AvitoParameter` |
| [`Operations.vue`](../../resources/js/Pages/Marketplace/Avito/Operations.vue) | заглушка |
| [`Show.vue`](../../resources/js/Pages/Marketplace/Avito/Show.vue) | заглушка карточки товара |

Composable: [`useAvitoCategoryFields.ts`](../../resources/js/Resources/composables/useAvitoCategoryFields.ts)
— загрузка параметров листовой категории и сохранение типа вручную.

> ⚠️ В `Categories.vue` результат composable **деструктурируется**:
> `const { fields: categoryFields, loading, categoryId, load, saveType } = useAvitoCategoryFields()`.
> Не обращайся к ref через `fields.loading` — это вызывает «вечный» спиннер.

Фильтры товаров: `show` (`active`/`archive`), `delivery` (`with`/`without`/все),
`search` (LIKE по `avito_products.name`, `avito_products.description`,
`products.code`, `products.code_search`). См.
[`AvitoProductRepository::getFilteredPaginated()`](../../app/Modules/Marketplace/Infrastructure/Persistence/AvitoProductRepository.php).

---

## 5. Что осталось сделать / открытые моменты

1. **Дополнить маппинг** [`AvitoProductFieldMap`](../../app/Modules/Marketplace/Application/Mappers/AvitoProductFieldMap.php):
   заполнить `MAP` для остальных полей товара (`Название объявления` → `name`,
   `Описание объявления` → `description`, …) и `SKIP` для колонок без соответствия.
2. **Карточка товара Авито** (`Show.vue` / `productShow`) — пока заглушка.
3. **Страница «Операции»** (`Operations.vue`) — пока заглушка.
4. **Выгрузка объявлений** (генерация фида) — не реализована.
5. **HTML-редактор** для `AvitoParameter` с `is_html = true` — сейчас простая textarea.
6. `syncCategories`/`syncFields` (API-синк) — legacy, кандидат на удаление.

---

## 6. Команды для применения

```bash
php artisan migrate            # применить миграции (в т.ч. avito_parameters)
php artisan db:seed            # запустить сидеры модуля (роли + AvitoParameter)
# или точечно:
php artisan db:seed --class=App\\Modules\\Marketplace\\Database\\Seeders\\AvitoParameterSeeder
```

Тестовые xlsx-файлы: `public/temp/avito_template.xlsx` (шаблон, один лист объявлений)
и `public/temp/avito_unload.xlsx` (большая выгрузка; для проверки гипотезы
использовать только первый лист объявлений — 2-й по счёту от начала).
