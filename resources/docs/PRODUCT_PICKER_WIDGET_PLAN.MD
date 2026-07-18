# План: ProductPicker для системы виджетов

## 1. Обзор задачи

Необходимо добавить возможность выбора товара (Product) в виджеты — по аналогии с ImagePicker, но без полноценного попап-окна с галереями.

**Требования:**
- Формат схемы виджета: `format: "product"`, `type: "integer"` (id товара) или `type: "object"` с полями id и доп. данными
- Когда товар выбран — показывать его первое изображение, артикул, название, кнопки "Удалить" / "Выбрать другой"
- При нажатии "Выбрать другой" — показывать поисковое поле (как в AddProduct.vue), найти по артикулу или названию
- Данные о товарах (изображения, артикул, название, цена) подгружаются через новый backend-сервис
- Совместимость с существующими DTO (WidgetFormFieldData)
- Backend на чистой архитектуре (UseCase + Entity + DTO), не привязанный к старому Eloquent напрямую

---

## 2. Что сделано ✅

### Итерация 1: Backend — расширение Catalog ProductRepository

| № | Файл | Статус |
|---|------|--------|
| 1 | `app/Modules/Catalog/Application/Interfaces/ProductRepositoryInterface.php` | ✅ Добавлен метод `search(string $query, int $limit = 10): array` |
| 2 | `app/Modules/Catalog/Infrastructure/Persistence/ProductRepository.php` | ✅ Реализован `search()` с поиском по `code`, `code_search`, `name` |

### Итерация 2: Backend — DTO и сервисы

| № | Файл | Статус |
|---|------|--------|
| 3 | `app/Modules/Content/Application/DTOs/ProductWidget/ProductSearchResultData.php` | ✅ Создан DTO с полями: id, name, code, code_search, url, short, price, quantity, in_stock, image_src, image_alt, image_next_src, image_next_alt |
| 4 | `app/Modules/Content/Application/Services/ProductSearchService.php` | ✅ Создан сервис: использует `ProductRepositoryInterface::search()` для поиска товаров и `PhotoRepositoryInterface::findByEntities()` для загрузки фото |

### Адаптация старого search_add

| № | Файл | Статус |
|---|------|--------|
| 5 | `app/Modules/Catalog/Controllers/ProductController.php` | ✅ В `search_add()` внедрён `ProductSearchService`, метод переписан |

---

## 3. Осталось сделать

### Итерация 3: Backend — доработка GetWidgetInstanceFormUseCase

| № | Файл | Действие |
|---|------|----------|
| 6 | `app/Modules/Content/Application/Actions/WidgetInstance/GetWidgetInstanceFormUseCase.php` | Добавить обработку `format: "product"` + загрузку данных о выбранном товаре через `ProductSearchService::getById()` |

### Итерация 4: Frontend — ProductPicker компонент

| № | Файл | Действие |
|---|------|----------|
| 7 | `resources/js/VueComponents/Content/ContentBlock/ProductPicker.vue` | Создать компонент |

### Итерация 5: Frontend — интеграция в WidgetFieldRenderer

| № | Файл | Действие |
|---|------|----------|
| 8 | `resources/js/VueComponents/Content/ContentBlock/WidgetFieldRenderer.vue` | Добавить рендеринг `format === 'product'` |

### Итерация 6: Frontend — SchemaEditor

| № | Файл | Действие |
|---|------|----------|
| 9 | `resources/js/Pages/Content/Widget/Elements/SchemaEditor.vue` | Добавить тип поля "Товар" |

---

## 4. Структура данных для виджета

### Формат в схеме (JSON Schema):
```json
{
  "product_field": {
    "type": "object",
    "format": "product",
    "title": "Товар",
    "properties": {
      "id": { "type": "integer", "title": "ID товара" },
      "name": { "type": "string", "title": "Название" },
      "url": { "type": "string", "title": "URL" },
      "short": { "type": "string", "title": "Краткое описание" },
      "price": { "type": "number", "title": "Цена" },
      "image_src": { "type": "string", "title": "URL изображения" },
      "image_alt": { "type": "string", "title": "Alt изображения" },
      "image_next_src": { "type": "string", "title": "URL второго изображения" },
      "image_next_alt": { "type": "string", "title": "Alt второго изображения" }
    }
  }
}
```

### Значение в params экземпляра:
```json
{
  "product_field": {
    "id": 123,
    "name": "Телевизор Samsung",
    "url": "/admin/catalog/product/123/edit",
    "short": "Краткое описание...",
    "price": 49999.00,
    "image_src": "/storage/products/123/thumb.jpg",
    "image_alt": "Телевизор Samsung",
    "image_next_src": "/storage/products/123/thumb2.jpg",
    "image_next_alt": "Телевизор Samsung вид сзади"
  }
}
```

### DTO ProductSearchResultData (на фронт):
```json
{
  "id": 123,
  "name": "Телевизор Samsung",
  "code": "TV-001",
  "code_search": "TV001",
  "url": "/admin/catalog/product/123/edit",
  "short": "Краткое описание товара...",
  "price": 49999.00,
  "quantity": 1,
  "in_stock": true,
  "image_src": "/storage/products/123/thumb.jpg",
  "image_alt": "Телевизор Samsung",
  "image_next_src": "/storage/products/123/thumb2.jpg",
  "image_next_alt": "Телевизор Samsung вид сзади"
}
```

---

## 5. Примечания

- При выборе товара в виджете **сохраняется только ID товара** в params.
- На фронте ProductPicker **самостоятельно подгружает** имя, артикул, изображение по ID товара через API `POST /admin/catalog/product/search_add`.
- При отображении виджета на сайте (Blade-шаблон) — данные о товаре берутся из БД по `$params['product_id']`.
- `search_add` переписан через `ProductSearchService` с использованием `ProductSearchResultData` и `PhotoRepositoryInterface::findByEntities()`.
