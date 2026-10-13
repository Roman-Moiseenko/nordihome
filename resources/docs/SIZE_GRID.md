# Инструкция: структура и привязка сетки размеров к товарам

## 1. Общая схема

Размерная сетка — это справочник, описывающий **оси** (измерения) и **значения** этих осей
(например, ось `size` — «Размер»: 30, 31, … 42; ось `width` — «Полнота»: D, E, EE).

Товар ссылается на размерную сетку через `products.size_grid_id`. Конкретные варианты размеров
товара хранятся в таблице `products_sizes` — по одной строке на каждую комбинацию осей
(декартово произведение значений осей).

Складской остаток (`stock`) может быть привязан к конкретному варианту размера через
`stock.products_sizes_id`.

```
size_grids 1 ──── * size_grid_values        (оси и их значения справочника)
        │
        │ products.size_grid_id (nullable)
        ▼
products 1 ──── * products_sizes 1 ──── * stock.products_sizes_id (nullable)
```

## 2. Таблицы

### 2.1. `size_grids`
| Поле | Тип | Описание |
|------|-----|----------|
| `id` | bigint PK | Идентификатор сетки |
| `name` | string | Название («Обувь взрослая», «Одежда мужская») |
| `dimensions` | json | Массив осей `[{code, name}, ...]` (порядок важен) |

Оси определяются VO [`SizeGridDimension`](app/Modules/Catalog/Domain/ValueObjects/SizeGridDimension.php:10).
Доступны по умолчанию: `size` («Размер») и `width` («Полнота»).

### 2.2. `size_grid_values`
Значения внутри одной оси.

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | bigint PK | |
| `size_grid_id` | FK → `size_grids` | cascadeOnDelete |
| `dimension_code` | string | Код оси (`size`, `width`) |
| `value` | string | Значение (`30`, `D`) |
| `label` | string|null | Подпись (`42 (EU)`), если null — показывается `value` |
| `sort_order` | int | Порядок вывода внутри оси |
| `is_active` | bool | Значение участвует в размерном ряде товара |

### 2.3. `products` (добавлено поле)
| Поле | Тип | Описание |
|------|-----|----------|
| `size_grid_id` | FK → `size_grids` nullable | Сетка, настроенная для товара. nullOnDelete |

### 2.4. `products_sizes`
Один вариант размера товара = одна комбинация осей.

| Поле | Тип | Описание |
|------|-----|----------|
| `id` | bigint PK | |
| `product_id` | FK → `products` | cascadeOnDelete |
| `values` | json | Комбинация осей: `{"size":"30","width":"D"}` |
| `is_active` | bool | Вариант доступен (`true`) или отключён (`false`) |

### 2.5. `stock` (добавлено поле)
| Поле | Тип | Описание |
|------|-----|----------|
| `products_sizes_id` | FK → `products_sizes` nullable | Связь остатка с конкретным размером. nullOnDelete |

## 3. Как варианты заполняются

Комбинации строятся перемножением **активных** значений осей (`is_active = true`) в порядке
объявления осей в `size_grids.dimensions`. Логика — в
[`SizeGridEntity::getCombinations()`](app/Modules/Catalog/Domain/Entities/SizeGridEntity.php:107).

- Одна ось (`size`): варианты `{"size":"30"}`, `{"size":"31"}`, …
- Две оси (`size`, `width`): варианты `{"size":"30","width":"D"}`, `{"size":"31","width":"D"}`, …

При выборе сетки для товара все варианты создаются сразу и становятся доступными (`is_active = true`).

## 4. API панели «Размерный ряд» (ProductEdit)

Маршруты:
- `GET admin/catalog/product/edit/size-grid/{id}` → [`ProductEditController::loadSizeGrid()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/ProductEditController.php:203)
- `POST admin/catalog/product/edit/size-grid/{id}` → [`ProductEditController::saveSizeGrid()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/ProductEditController.php:209)

Цепочка:
- Query [`GetProductSizesQuery`](app/Modules/Catalog/Application/Actions/Product/EditPage/GetProductSizesQuery.php:20)
  возвращает доступные сетки, выбранную сетку и текущие варианты товара.
- UseCase [`SetProductSizesUseCase`](app/Modules/Catalog/Application/Actions/Product/EditPage/SetProductSizesUseCase.php:20):
  - `sizeGridId = null` → очищает размерный ряд и отвязывает сетку;
  - `sizeGridId` задан → сохраняет связь товара с сеткой и перестраивает `products_sizes`.

Хранение/перестройка — [`ProductSizeRepository`](app/Modules/Catalog/Infrastructure/Persistence/ProductSizeRepository.php:16):
- `rebuildForProduct()`: создаёт недостающие варианты, сохраняет `is_active` существующих,
  удаляет варианты, исчезнувшие из сетки (с обнулением `stock.products_sizes_id`);
- `clearForProduct()`: удаляет все варианты товара (с обнулением связанного `stock`).

## 5. Перестройка при изменении сетки

При изменении справочника сетки ([`UpdateSizeGridUseCase`](app/Modules/Catalog/Application/Actions/SizeGrid/UpdateSizeGridUseCase.php:22))
после сохранения перебираются **все товары**, у которых настроена эта сетка
(`ProductSizeRepository::getProductIdsBySizeGridId()`), и для каждого вызывается `rebuildForProduct()`.

Если какое-то значение оси было удалено или деактивировано — соответствующие строки
`products_sizes` удаляются, а связанные записи `stock` обнуляются (`products_sizes_id = null`).

При удалении сетки ([`RemoveSizeGridUseCase`](app/Modules/Catalog/Application/Actions/SizeGrid/RemoveSizeGridUseCase.php:12))
размерный ряд товаров очищается, а `products.size_grid_id` обнуляется FK (`nullOnDelete`).

## 6. Фронтенд

Панель [`resources/js/Pages/Catalog/Product/Panels/SizeGrid.vue`](resources/js/Pages/Catalog/Product/Panels/SizeGrid.vue:1):

- Если сетка не выбрана — селект со списком сеток и кнопка «Применить».
- Одна ось (`size`) — квадратики размеров идут друг за другом, переносятся на новую строку,
  не более 6 в строке. Клик по квадрату переключает доступность (серый = недоступен).
- Две оси (`size` + `width`) — `width` образует колонки, внутри колонки идут размеры.
  Клик по заголовку колонки отключает/включает все размеры этой оси.
- Кнопка «Сохранить размеры» отправляет изменённые `isActive` на сервер.
