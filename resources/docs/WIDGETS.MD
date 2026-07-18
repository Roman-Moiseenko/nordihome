# Система виджетов (Widget System)

## 1. Общая архитектура

Система виджетов состоит из трёх уровней:

```
Widget (тип/шаблон виджета) 
  └── WidgetInstance (экземпляр виджета с конкретными параметрами)
        └── ContentBlock (привязка экземпляра к странице/сущности)
```

### 1.1. Сущности (модели)

#### Widget — тип виджета (Eloquent Model: `App\Modules\Content\Infrastructure\Models\Widget`)

Хранит описание «типа» виджета. Это аналог «класса» или «прототипа» виджета.

Поля модели:
| Поле | Тип | Описание |
|------|-----|----------|
| `id` | integer | Первичный ключ |
| `name` | string | Название виджета (человекочитаемое) |
| `slug` | string | Уникальный URL-идентификатор, используется для формирования пути к Blade-шаблону |
| `description` | string|null | Описание виджета |
| `category` | string | Категория виджета (content, media, commerce, container, custom) |
| `schema` | array (json) | JSON Schema, описывающая поля виджета |
| `created_at` | timestamp | Дата создания |
| `updated_at` | timestamp | Дата обновления |

Связи:
- `instances()` — hasMany → `WidgetInstance`

**Схема (schema)** — это JSON Schema в формате:
```json
{
  "type": "object",
  "properties": {
    "field_name": {
      "type": "string",
      "title": "Название поля",
      "default": "",
      "format": "html|uri|uuid|...",
      "minimum": 0,
      "maximum": 100,
      "enum": ["option1", "option2"]
    }
  },
  "required": ["field_name"]
}
```

Используется класс `WidgetSchema` (ValueObject), который валидирует, что корень содержит `type: "object"` и поле `properties`.

Доступные категории (value object `WidgetCategory`):
- `content` — Контент
- `media` — Медиа
- `commerce` — Магазин
- `container` — Контейнеры
- `custom` — Собственный

**Шаблон (Blade):** Каждый виджет ассоциируется с Blade-шаблоном по пути:
```
widgets/{category}/{slug}.blade.php
```
Шаблон хранится отдельно (в файловой системе или БД) и редактируется через BladeEditor.

---

#### WidgetInstance — экземпляр виджета (Eloquent Model: `App\Modules\Content\Infrastructure\Models\WidgetInstance`)

Хранит конкретный «заполненный» экземпляр виджета с реальными значениями полей.

Поля модели:
| Поле | Тип | Описание |
|------|-----|----------|
| `id` | integer | Первичный ключ |
| `widget_id` | integer | FK → `widgets.id` — ссылка на тип виджета |
| `params` | array (json) | Значения полей согласно схеме виджета (ключ → значение) |
| `title` | string|null | Дополнительный заголовок экземпляра |
| `created_at` | timestamp | Дата создания |
| `updated_at` | timestamp | Дата обновления |

Связи:
- `widget()` → belongsTo → `Widget`
- `contentBlocks()` → hasMany → `ContentBlock`

**params** — ассоциативный массив, где ключи соответствуют именам полей из schema виджета, а значения — конкретные данные (строки, числа, булевы, массивы, вложенные объекты).

Пример:
```json
{
  "title": "Акция недели",
  "image": {
    "id": 123,
    "src": "/storage/photo.jpg",
    "alt": "Акция",
    "title": "Акция недели",
    "description": ""
  },
  "columns": 3,
  "items": [
    {"text": "Преимущество 1", "icon": "/storage/icon1.png"},
    {"text": "Преимущество 2", "icon": "/storage/icon2.png"}
  ]
}
```

---

#### ContentBlock — блок контента на странице (Eloquent Model: `App\Modules\Content\Infrastructure\Models\ContentBlock`)

Связывает экземпляр виджета с конкретной сущностью (страницей, записью, товаром и т.д.) и определяет его позицию.

Поля модели:
| Поле | Тип | Описание |
|------|-----|----------|
| `id` | integer | Первичный ключ |
| `container_type` | string | Тип родительской сущности (morph — post, page, product и т.д.) |
| `container_id` | integer | ID родительской сущности |
| `widget_instance_id` | integer|null | FK → `widget_instances.id` |
| `sort_order` | integer | Порядок сортировки |
| `section` | string|null | Секция страницы (header, body, sidebar, footer) |
| `caption` | string|null | Заголовок блока (для админки) |
| `active` | boolean | Активен ли блок (true/false) |
| `created_at` | timestamp | Дата создания |
| `updated_at` | timestamp | Дата обновления |

Связи:
- `widgetInstance()` → belongsTo → `WidgetInstance`
- `container()` → morphTo → родительская сущность (полиморфная связь)

---

## 2. Создание виджета (Widget)

Создание виджета происходит в два этапа:

### Этап 1: Определение типа виджета

**Страница:** `/admin/content/widget`
**Компонент:** `resources/js/Pages/Content/Widget/Index.vue`

Список виджетов с CRUD-операциями. При создании указываются:
- `name` — название
- `slug` — идентификатор для URL и пути шаблона
- `category` — категория (выбор из списка категорий, загружаемых через `contentStore`)

### Этап 2: Редактирование виджета

**Страница:** `/admin/content/widget/{id}`
**Компонент:** `resources/js/Pages/Content/Widget/Show.vue`

Содержит три раздела:

#### 2.1. Основная информация
Поля: название, slug, категория, описание.
Используется Inertia-роутер для сохранения (PUT-запрос на `admin.content.widget.update`).

Механизм отслеживания изменений:
- `changeInfo` — реактивная переменная, отслеживающая расхождения между `form` и `initialForm`, а также изменения в `schemaConfig`.

#### 2.2. Редактор схемы (SchemaEditor)

**Компонент:** `resources/js/Pages/Content/Widget/Elements/SchemaEditor.vue`

Позволяет визуально конструировать JSON Schema для полей виджета. Структура:

- **SchemaEditor** — главный контейнер
  - Список полей (аккордеон), каждое сворачиваемое
  - Кнопка «Добавить поле» с диалогом настройки
  - **PropertyAttributesEditor** — редактор атрибутов конкретного поля
    - Ключ (имя поля)
    - type (string, integer, number, boolean, array, object)
    - title (человекочитаемое название)
    - format (html, image, uri, date, date-time, widget, color)
    - default (значение по умолчанию)
    - minimum / maximum (для чисел)
    - enum (для строк — выпадающий список)
    - required (переключатель)
    - Для type=array:
      - **ItemPropertiesEditor** — редактор вложенных полей для элементов массива
    - Для type=object:
      - **ItemPropertiesEditor** — редактор вложенных полей объекта

**Типы полей при добавлении (через диалог SchemaEditor):**

| Тип в диалоге | Результирующий JSON Schema | Поведение на фронте |
|--------------|---------------------------|---------------------|
| Строка (string) | `{ type: "string" }` | Обычное текстовое поле |
| Текст HTML | `{ type: "string", format: "html" }` | textarea |
| Изображение | `{ type: "object", format: "image", properties: { id, src, alt, title, description } }` | ImagePicker |
| Ссылка | `{ type: "string", format: "uri" }` | Поле для URL |
| Число целое | `{ type: "integer" }` | Input number |
| ID виджета | `{ type: "integer", format: "widget" }` | Выбор виджета |
| Число дробное | `{ type: "number" }` | Input number |
| Да/Нет | `{ type: "boolean" }` | Switch |
| Объект | `{ type: "object", properties: {} }` | Вложенные поля |
| Массив объектов | `{ type: "array", items: { type: "object", properties: {...} } }` | Список элементов с добавлением |
| Массив изображений | `{ type: "array", items: { type: "object", format: "image", properties: { id, src, alt, title, description } } }` | Список изображений |
| Массив строк | `{ type: "array", items: { type: "string" } }` | Список строк |
| Массив чисел | `{ type: "array", items: { type: "integer" } }` | Список чисел |

#### 2.3. Редактор шаблона (BladeEditor)

**Компонент:** `resources/js/Pages/Content/Widget/Elements/BladeEditor.vue`

Редактор кода на основе CodeMirror с поддержкой PHP (Blade) и HTML.
Путь к файлу шаблона: `widgets/{category}/{slug}.blade.php`

Сохранение шаблона происходит отдельно от информации через POST-запрос на `admin.content.widget.save-template`.

---

## 3. Работа с ContentBlock (привязка виджетов к сущностям)

### 3.1. Компонент ContentBlockEditor

**Путь:** `resources/js/VueComponents/Content/ContentBlock/ContentBlockEditor.vue`

Этот компонент используется на страницах сущностей (Post, Page и т.д.) для управления блоками контента.

**Пример использования (страница Post):**
```vue
<ContentBlockEditor
    :blocks="blocks || []"
    :container-id="post.id"
    container-type="post"
/>
```

Параметры:
- `blocks` — массив блоков (ContentBlockData)
- `container-id` — ID сущности, к которой привязываются блоки
- `container-type` — тип сущности (post, page, product и т.д.)

**Логика работы:**

1. **Загрузка:** Переданные блоки отображаются в виде списка (свернуты по умолчанию)
2. **Создание блока:** POST-запрос на `admin.content.content-blocks.store` с `container_type` и `container_id`. Новый блок создаётся без виджета.
3. **Редактирование:** Изменение caption и section (PUT-запрос на `admin.content.content-blocks.update`)
4. **Удаление:** DELETE-запрос на `admin.content.content-blocks.destroy`
5. **Сортировка:** Drag & Drop через SortableJS, после перестановки — POST на `admin.content.content-blocks.sort`
6. **Toggle активности:** POST на `admin.content.content-blocks.toggle`

### 3.2. Компонент ContentBlockItem

**Путь:** `resources/js/VueComponents/Content/ContentBlock/ContentBlockItem.vue`

Отображает один блок контента. Внутри него:

- **Если нет виджета** — отображается пустая область с кнопкой «+ Добавить Виджет»
- **Если виджет есть** — отображается WidgetFieldRenderer с полями формы

При разворачивании свернутого блока автоматически загружаются поля формы через API.

### 3.3. Компонент WidgetSelectorDialog

**Путь:** `resources/js/VueComponents/Content/ContentBlock/WidgetSelectorDialog.vue`

Диалог выбора виджета. Виджеты сгруппированы по категориям (берутся из `contentStore.widgets`). Категории и виджеты загружаются через API-роуты:
- `admin.content.widget.categories` — список категорий
- `admin.content.widget.widgets` — виджеты, сгруппированные по категориям

### 3.4. Компонент WidgetFieldRenderer

**Путь:** `resources/js/VueComponents/Content/ContentBlock/WidgetFieldRenderer.vue`

Рендерит поля формы на основе метаинформации `WidgetFormFieldData`, полученной из API.

**Логика рендеринга полей:**

| Условие | Отображаемый элемент |
|---------|---------------------|
| `field.format === 'html'` | textarea (4 строки) |
| `field.format === 'color'` | ColorPicker |
| `field.format === 'widget'` | Выбор вложенного виджета |
| `field.format === 'image'` | ImagePicker (для object) |
| `field.type === 'string'` и длинное значение | textarea |
| `field.type === 'string'` | обычный input |
| `field.options` (enum) | Select |
| `field.type === 'boolean'` | Switch |
| `field.type === 'integer' \|\| 'number'` | InputNumber |
| `field.type === 'array'` с nestedFields | Список элементов с добавлением/удалением |
| `field.type === 'object'` с nestedFields | Вложенный WidgetFieldRenderer |

Поля делятся на три группы:
- **Fullwidth** — html, длинные строки, обычные строки без формата (на всю ширину)
- **Compact** — всё остальное простое (в ряд)
- **Composite** — object/array с вложенными полями (отдельно внизу)

**Сохранение:** По кнопке «Сохранить» отправляется PUT-запрос на `admin.content.widget-instances.update` с актуальными params.

### 3.5. Компонент ImagePicker

**Путь:** `resources/js/VueComponents/Content/ContentBlock/ImagePicker.vue`

Полноценный интерфейс выбора изображения из галерей:
- Левая панель: список галерей
- Центр: сетка изображений с загрузкой новых
- Правая панель: информация о выбранном изображении (alt, title, description)

Загруженные изображения попадают в галерею «Виджет».

---

## 4. Backend — преобразование схемы в форму

### 4.1. UseCase: GetWidgetInstanceFormUseCase

**Путь:** `app/Modules/Content/Application/Actions/WidgetInstance/GetWidgetInstanceFormUseCase.php`

Этот сервис отвечает за преобразование JSON Schema виджета + текущих params экземпляра в массив `WidgetFormFieldData[]`, который фронт использует для рендеринга формы.

**Алгоритм работы:**
1. Загружает экземпляр (WidgetInstance) по ID
2. Загружает тип виджета (Widget) по `widget_id`
3. Извлекает `schema` (свойства + обязательные поля)
4. Извлекает текущие `params`
5. Рекурсивно обходит `properties` схемы и для каждого поля создаёт `WidgetFormFieldData`

**Рекурсивная обработка:**
- Для `type: "object"` — создаёт вложенные поля (`nestedFields`) и рекурсивно вызывает себя
- Для `type: "array"` с `items: { type: "object" }` — создаёт вложенные поля для структуры одного элемента
- Для `format: "image"` — гарантирует наличие полей id, src, alt, title, description (даже если их нет в схеме)
- Для точечной нотации (вложенные объекты) — извлекает значения через `getDotValue()`

### 4.2. Data Transfer Object: WidgetFormFieldData

**Путь:** `app/Modules/Content/Application/DTOs/WidgetInstance/WidgetFormFieldData.php`

```php
class WidgetFormFieldData {
    public string $name;       // ключ в params
    public string $type;       // string | integer | boolean | array | object
    public string $label;      // из title схемы
    public mixed $value;       // текущее значение из params
    public mixed $default;     // значение по умолчанию из схемы
    public bool $required;     // обязательное поле?
    public ?string $format;    // color | widget | html | uri | image | ...
    public ?array $options;    // для enum-полей
    public ?array $nestedFields; // для type === 'object' — массив WidgetFormFieldData[]
}
```

---

## 5. API-маршруты (REST)

Все маршруты находятся в группе `/admin/content/` с префиксом `admin.content.`.

### Widget (типы виджетов)

| Метод | URL | Имя роута | Описание |
|-------|-----|-----------|----------|
| GET | `/admin/content/widget` | `widget.index` | Список виджетов |
| POST | `/admin/content/widget` | `widget.store` | Создать виджет |
| GET | `/admin/content/widget/{id}` | `widget.show` | Просмотр/редактирование виджета |
| PUT | `/admin/content/widget/{id}` | `widget.update` | Обновить виджет |
| DELETE | `/admin/content/widget/{id}` | `widget.destroy` | Удалить виджет |
| GET | `/admin/content/widget/categories` | `widget.categories` | Список категорий (JSON) |
| GET | `/admin/content/widget/widgets` | `widget.widgets` | Виджеты сгруппированные (JSON) |
| GET | `/admin/content/widget/{id}/template` | `widget.template` | Получить содержимое шаблона |
| POST | `/admin/content/widget/{id}/template` | `widget.save-template` | Сохранить шаблон |

### WidgetInstance (экземпляры виджетов)

| Метод | URL | Имя роута | Описание |
|-------|-----|-----------|----------|
| GET | `/admin/content/widget-instances/{id}` | `widget-instances.show` | Получить экземпляр с полями формы |
| POST | `/admin/content/widget-instances` | `widget-instances.store` | Создать экземпляр |
| PUT | `/admin/content/widget-instances/{id}` | `widget-instances.update` | Обновить params |
| DELETE | `/admin/content/widget-instances/{id}` | `widget-instances.destroy` | Удалить экземпляр |

### ContentBlock (блоки контента)

| Метод | URL | Имя роута | Описание |
|-------|-----|-----------|----------|
| GET | `/admin/content/content-blocks/{id}` | `content-blocks.show` | Получить блок |
| POST | `/admin/content/content-blocks` | `content-blocks.store` | Создать блок |
| PUT | `/admin/content/content-blocks/{id}` | `content-blocks.update` | Обновить caption/section |
| DELETE | `/admin/content/content-blocks/{id}` | `content-blocks.destroy` | Удалить блок |
| POST | `/admin/content/content-blocks/sort` | `content-blocks.sort` | Сортировка |
| POST | `/admin/content/content-blocks/{id}/toggle` | `content-blocks.toggle` | Переключить active |

---

## 6. Типовые сценарии использования

### 6.1. Создание нового типа виджета

1. Администратор переходит на `/admin/content/widget`
2. Нажимает «Добавить виджет», заполняет name, slug, category
3. После сохранения — редирект на страницу виджета (`widget.show`)
4. В SchemaEditor добавляет поля согласно JSON Schema
5. В BladeEditor пишет Blade-шаблон для отображения на сайте
6. Сохраняет изменения

### 6.2. Добавление виджета на страницу (например, Post)

1. Администратор открывает страницу записи (`/admin/content/post/{id}`)
2. Находит секцию «Блоки контента» (ContentBlockEditor)
3. Нажимает «+ Добавить блок» — создаётся пустой ContentBlock
4. Разворачивает новый блок, нажимает «+ Добавить Виджет»
5. Выбирает нужный тип виджета из списка (WidgetSelectorDialog)
6. Создаётся WidgetInstance (POST `/widget-instances`), привязывается к ContentBlock
7. Автоматически загружаются поля формы (через `getWidgetInstance`)
8. Заполняет поля и сохраняет (PUT `/widget-instances/{id}`)

### 6.3. Отрисовка виджета на сайте

Шаблон виджета находится по пути `widgets/{category}/{slug}.blade.php`.
В шаблоне доступны:
- `$params` — массив значений полей экземпляра
- `$widget` — объект виджета (типа)

Пример Blade-шаблона:
```blade
{{-- widgets/content/my_widget.blade.php --}}
@if(!empty($params['title']))
    <h2>{{ $params['title'] }}</h2>
@endif

@if(!empty($params['image']['src']))
    <img src="{{ $params['image']['src'] }}" alt="{{ $params['image']['alt'] ?? '' }}">
@endif
```

---

## 7. Состав компонентов фронта

### Страница создания/редактирования виджета

```
Pages/Content/Widget/
├── Index.vue                     — Список виджетов
├── Show.vue                      — Карточка виджета (основная + схема + шаблон)
└── Elements/
    ├── SchemaEditor.vue           — Редактор JSON Schema (список полей)
    ├── PropertyAttributesEditor.vue — Редактор атрибутов одного поля
    ├── ItemPropertiesEditor.vue   — Редактор вложенных свойств (для object/array items)
    ├── SchemaArrayObjectEditor.vue — Редактор значений массива объектов (для заполнения)
    ├── SchemaArrayPrimitiveEditor.vue — Редактор значений массива примитивов
    └── BladeEditor.vue            — Редактор Blade-шаблона (CodeMirror)
```

### Компоненты управления блоками контента

```
VueComponents/Content/ContentBlock/
├── ContentBlockEditor.vue        — Главный редактор блоков (список + сортировка)
├── ContentBlockItem.vue          — Один блок (виджет + поля)
├── WidgetSelectorDialog.vue      — Диалог выбора типа виджета
├── WidgetFieldRenderer.vue       — Рендеринг полей формы из схемы
└── ImagePicker.vue               — Выбор изображения из галерей
```

---

## 8. Хранилище данных (Pinia Store)

**Файл:** `resources/js/Resources/contentStore.ts`

Глобальное хранилище `useContentStore` содержит:
- `categories` — список категорий виджетов (загружается с `admin.content.widget.categories`)
- `widgets` — виджеты, сгруппированные по категориям (загружается с `admin.content.widget.widgets`)
- `sections` — варианты секций для ContentBlock (header, body, sidebar, footer)
- `reload()` — метод для перезагрузки данных

**Composable:** `resources/js/Resources/composables/useContentBlock.ts`

Содержит все API-методы для работы с ContentBlock и WidgetInstance:
- `createBlock`, `getBlock`, `updateBlock`, `sortBlock`, `deleteBlock`, `toggleBlock`
- `createWidgetInstance`, `getWidgetInstance`, `updateWidgetInstance`, `deleteWidgetInstance`

---

## 9. Примеры схем (из widget_example.json)

В файле `resources/docs/widget_example.json` собраны готовые примеры JSON Schema для различных типов виджетов:

1. **Текстовый блок с колонками** — string (title), integer с min/max (columns), string с format:html (content_N)
2. **Два изображения с ссылками** — string с format:uuid (image), string с format:uri (link), string (alt)
3. **Блок с изображением, текстом и кнопками** — string/uuid, string/html, string/uri
4. **Блок с иконкой и списком преимуществ** — string/uuid + array of objects с вложенными свойствами
5. **Слайдер изображений** — boolean (autoplay), integer (interval), array of objects (slides)
6. **Блок с видео** — string/uri (video_url), string/uuid (cover_image), boolean (autoplay)
7. **Блок товаров** — array of integers (product_ids), string с enum (display_mode), integer (limit)
8. **Блок с отзывами** — array of objects (reviews) с author, avatar/uuid, text/html, rating/integer
9. **Форма обратной связи** — string/html, array of objects (fields) с name, label, type (enum), required, placeholder
10. **Карта** — number (latitude, longitude), integer (zoom), string (title, address)
11. **Заголовок** — string (content), string с enum (text_align)
