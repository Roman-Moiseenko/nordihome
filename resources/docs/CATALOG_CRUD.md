# Каталог: сохранение, отображение и структура (на примере Category / Equivalent / Series / Group)

> Документ описывает, как устроены страницы каталога в админке: как данные попадают на
> Vue-страницу, как отображаются, как сохраняются и как связаны между собой.
>
> Разбираемые примеры:
> - [`Category/Show`](resources/js/Pages/Catalog/Category/Show.vue:1)
> - [`Equivalent/Show`](resources/js/Pages/Catalog/Equivalent/Show.vue:1)
> - [`Series/Index`](resources/js/Pages/Catalog/Series/Index.vue:1)
> - [`Group/Index`](resources/js/Pages/Catalog/Group/Index.vue:1)
> - [`Group/Show`](resources/js/Pages/Catalog/Group/Show.vue:1)

---

## 1. Общая архитектура

Проект построен по принципам **Clean Architecture** внутри модуля [`app/Modules/Catalog`](app/Modules/Catalog) и
отдаёт данные на фронтенд через **Inertia + Vue 3 (`<script setup lang="ts">`) + Element Plus + Ziggy**.

```
Vue-страница (resources/js/Pages/Catalog/...)
        │  Inertia::render(...)  →  props
        ▼
Контроллер (Presentation/Http/Controllers/Web)
        │  DTO::validateAndCreate() / Query / UseCase
        ▼
Application (DTOs + Actions: Query/UseCase)
        │  Domain\Interfaces\...RepositoryInterface
        ▼
Infrastructure (Persistence\...Repository → Eloquent Model → БД)
```

Права проверяются в `Query`/`UseCase` через [`UserPermission::can()`](app/Modules/Shared/Domain/Entities/UserPermission.php):
`catalog.product.view`, `catalog.product.create`, `catalog.product.edit`, `catalog.category.edit` и т.д.

Детальное описание слоёв — в [`INSTRUCTION.md`](resources/docs/INSTRUCTION.md:1).

---

## 2. Структура Vue-страниц каталога

Одна сущность каталога, как правило, имеет два типа страниц:

| Тип | Назначение | Примеры |
|-----|-----------|---------|
| **Index** | список + фильтры + создание + удаление | [`Series/Index`](resources/js/Pages/Catalog/Series/Index.vue:1), [`Group/Index`](resources/js/Pages/Catalog/Group/Index.vue:1) |
| **Show** | карточка сущности + вкладки | [`Category/Show`](resources/js/Pages/Catalog/Category/Show.vue:1), [`Equivalent/Show`](resources/js/Pages/Catalog/Equivalent/Show.vue:1), [`Group/Show`](resources/js/Pages/Catalog/Group/Show.vue:1) |

Файловая раскладка страницы (на примере `Category`):

```
resources/js/Pages/Catalog/Category/
├── Index.vue             # список категорий
├── Show.vue              # карточка категории
├── Block/
│   └── Info.vue          # форма основных полей (сохранение)
└── Panels/
    ├── Children.vue      # вкладка "Подкатегории"
    ├── Attributes.vue    # вкладка "Атрибуты"
    ├── Products.vue      # вкладка "Товары"
    └── Blocks.vue        # вкладка "Контент блоки"
```

---

## 3. Index-страница (список)

Шаблон [`Series/Index`](resources/js/Pages/Catalog/Series/Index.vue:1) и [`Group/Index`](resources/js/Pages/Catalog/Group/Index.vue:1)
идентичен и состоит из блоков:

1. **Заголовок** `<h1>` и `<Head><title>`.
2. **Создание** — [`el-popover`](resources/js/Pages/Catalog/Series/Index.vue:6) с полем ввода и кнопкой:
   ```js
   function createButton() {
       router.post(route('admin.catalog.series.store', { name: new_series.value }))
   }
   ```
   Для группы — [`router.post(route('admin.catalog.group.store', { name: new_group.value }))`](resources/js/Pages/Catalog/Group/Index.vue:97).
3. **Фильтры** — [`TableFilter`](resources/js/VueComponents/TableFilter.vue) + `reactive`-объект `filter`,
   поля синхронизированы с пропсами `filters` (см. [`filter`](resources/js/Pages/Catalog/Series/Index.vue:84)).
4. **Таблица** — [`el-table`](resources/js/Pages/Catalog/Series/Index.vue:27), источник данных:
   ```js
   const tableData = ref([...props.series.data])
   ```
   Клик по строке ведёт на карточку: [`routeClick`](resources/js/Pages/Catalog/Series/Index.vue:97) → `router.get(route('admin.catalog.series.show', { id: row.id }))`.
5. **Удаление** — инъекция `inject("$delete_entity")` + модалка [`DeleteEntityModal`](resources/js/VueComponents/DialogDeleteEntity.vue):
   ```js
   function handleDeleteEntity(row) {
       $delete_entity.show(route('admin.catalog.series.destroy', { id: row.id }));
   }
   ```
6. **Пагинация** — компонент [`Pagination`](resources/js/VueComponents/Pagination.vue), получает
   `current_page / per_page / total` из пропса-пагинатора.

### Пропсы Index-страницы

Контроллер возвращает пагинатор и фильтры:

- [`SeriesController::index()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/SeriesController.php:43)
  ```php
  $filters = FilterSeriesIndexData::validateAndCreate($request->all());
  $series  = $this->indexSeriesQuery->execute($filters, $userPermission);
  return Inertia::render('Catalog/Series/Index', [
      'series'  => $series,
      'filters' => $filters,
  ]);
  ```
- [`GroupController::index()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/GroupController.php:41) — аналогично,
  фильтры [`FilterGroupIndexData`](app/Modules/Catalog/Application/DTOs/Group/FilterGroupIndexData.php:9).

`Query` собирает пагинатор из репозитория и дополняет каждую запись агрегатом `quantity` (кол-во товаров):

- [`IndexSeriesQuery::execute()`](app/Modules/Catalog/Application/Actions/Series/IndexSeriesQuery.php:23) → `countProductsBySeriesIds`
- [`IndexGroupQuery::execute()`](app/Modules/Catalog/Application/Actions/Group/IndexGroupQuery.php:23) → `countProductsByGroupIds`
- [`IndexEquivalentQuery::execute()`](app/Modules/Catalog/Application/Actions/Equivalent/IndexEquivalentQuery.php:25) → `countProductsByEquivalentIds` + имена категорий

Индексные DTO:
- [`SeriesIndexData`](app/Modules/Catalog/Application/DTOs/Series/SeriesIndexData.php:9) (`id`, `name`, `quantity`)
- [`GroupIndexData`](app/Modules/Catalog/Application/DTOs/Group/GroupIndexData.php:9) (`id`, `name`, `quantity`, `published`, `description`)
- [`EquivalentIndexData`](app/Modules/Catalog/Application/DTOs/Equivalent/EquivalentIndexData.php:9) (`id`, `name`, `category`, `quantity`)

---

## 4. Show-страница (карточка)

Шаблон [`Group/Show`](resources/js/Pages/Catalog/Group/Show.vue:1) (и аналогично [`Category/Show`](resources/js/Pages/Catalog/Category/Show.vue:1)):

```
<Head> <el-config-provider :locale="ru">
├── Заголовок h1 + кнопка "Помощь" (HelpBlock)
├── Блок <GroupInfo :group="group" />          ← форма + кнопки Сохранить/Отмена
└── <el-tabs>
    ├── <PanelProducts :group-id="group.id" />  ← вкладка "Товары"
    └── <PanelBlocks ... />                      ← вкладка "Контент блоки"
```

### 4.1 Пропсы Show-страницы

Контроллер формирует сущность через `Query` и DTO `fromEntity()`:

- [`GroupController::show()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/GroupController.php:60)
  ```php
  $groupEntity = $this->viewGroupQuery->execute($id, $userPermission);
  $blocks = $this->listContentBlockByContainerUseCase->execute(
      new ContentBlockContainerData($id, ContainerType::GROUP)
  );
  return Inertia::render('Catalog/Group/Show', [
      'group'  => GroupViewData::fromEntity($groupEntity),
      'blocks' => $blocks,
  ]);
  ```

View-DTO (то, что приходит в `props` Vue):
- [`GroupViewData`](app/Modules/Catalog/Application/DTOs/Group/GroupViewData.php:14) (`id`, `name`, `slug`, `description`, `published`)
- [`CategoryViewData`](app/Modules/Catalog/Application/DTOs/Category/CategoryViewData.php:12) — дополнительно `meta`, `parentId`, `left/right/depth`, рекурсивные `children`
- [`EquivalentViewData`](app/Modules/Catalog/Application/DTOs/Equivalent/EquivalentViewData.php) — `id`, `name`, `categoryId`
- [`SeriesViewData`](app/Modules/Catalog/Application/DTOs/Series/SeriesViewData.php) — сущность + товары серии

---

## 5. Сохранение (блок `Block/Info.vue`)

Ключевой паттерн — форма с «грязным» состоянием. Пример [`Group/Block/Info.vue`](resources/js/Pages/Catalog/Group/Block/Info.vue:35):

```js
// Эталон из пропсов (для сравнения и отмены)
const initialInfo = reactive({
    name: props.group.name,
    slug: props.group.slug ?? '',
    description: props.group.description ?? '',
    published: !!props.group.published,
})

// Рабочая копия, к которой привязаны поля формы
const info = reactive({ ...initialInfo })

// Есть ли изменения — определяет видимость кнопок
const hasChanges = computed(() =>
    ['name', 'slug', 'description', 'published'].some(
        key => JSON.stringify(info[key]) !== JSON.stringify(initialInfo[key])
    )
)

function onCancel() {
    Object.assign(info, { ...initialInfo })
}

function onSetInfo() {
    router.visit(
        route('admin.catalog.group.update', { id: props.group.id }), {
            method: 'put',
            data: { ...info },
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // обновляем эталон — кнопки скроются
                Object.assign(initialInfo, JSON.parse(JSON.stringify(info)))
            }
        }
    )
}
```

Аналогично устроены:
- [`Category/Block/Info.vue`](resources/js/Pages/Catalog/Category/Block/Info.vue:49) (метод `PUT` на `admin.catalog.category.update`, `preserveState: false`)
- [`Equivalent/Block/Info.vue`](resources/js/Pages/Catalog/Equivalent/Block/Info.vue:24)

### 5.1 Обратный путь (Backend-сохранение)

1. **Контроллер** валидирует и создаёт DTO, вызывает `UseCase`, делает `redirect` с flash-сообщением:
   [`GroupController::update()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/GroupController.php:73)
   ```php
   $dto   = GroupUpdateData::validateAndCreate($request->all());
   $group = $this->updateGroupUseCase->execute($id, $dto, $userPermission);
   return redirect()->route('admin.catalog.group.show', $group->id)->with('success', 'Сохранено');
   ```

2. **DTO** — Spatie Laravel Data, описывает допустимые поля и правила валидации.
   Примеры:
   - [`CategoryUpdateData`](app/Modules/Catalog/Application/DTOs/Category/CategoryUpdateData.php:14)
   - [`GroupUpdateData`](app/Modules/Catalog/Application/DTOs/Group/GroupUpdateData.php)
   - [`SeriesCreateData`](app/Modules/Catalog/Application/DTOs/Series/SeriesCreateData.php:13) / [`SeriesUpdateData`](app/Modules/Catalog/Application/DTOs/Series/SeriesUpdateData.php:13)

3. **UseCase** — проверяет права, достаёт сущность, применяет изменения, сохраняет.
   [`UpdateGroupUseCase::execute()`](app/Modules/Catalog/Application/Actions/Group/UpdateGroupUseCase.php:23):
   ```php
   if (!$userPermission->can('catalog.product.edit')) throw new AccessDeniedException();
   $group = $this->groupRepository->getById($groupId);
   $group->name = $dto->name;
   // slug: пустой → генерируется из названия, конфликт → + '-' . uniqid()
   ...
   $dto->published ? $group->publish() : $group->unpublish();
   return $this->groupRepository->save($group);
   ```
   [`UpdateCategoryUseCase::execute()`](app/Modules/Catalog/Application/Actions/Category/UpdateCategoryUseCase.php:24) дополнительно обрабатывает `parentId`, `svgIcon` и `meta`.

4. **Repository** (`Infrastructure/Persistence`) мапит Entity → Eloquent и обратно.
   - [`GroupRepository::save()`](app/Modules/Catalog/Infrastructure/Persistence/GroupRepository.php:52) / [`hydrate()`](app/Modules/Catalog/Infrastructure/Persistence/GroupRepository.php:116)
   - [`CategoryRepository::save()`](app/Modules/Catalog/Infrastructure/Persistence/CategoryRepository.php:30) / [`hydrate()`](app/Modules/Catalog/Infrastructure/Persistence/CategoryRepository.php:184) / [`hydrateWithChildren()`](app/Modules/Catalog/Infrastructure/Persistence/CategoryRepository.php:214)

5. **Eloquent-модель** — финальная точка записи.
   - [`Group`](app/Modules/Catalog/Infrastructure/Models/Group.php:20), `$fillable = ['name','description','slug','published']`
   - [`Category`](app/Modules/Catalog/Infrastructure/Models/Category.php:32) (`NodeTrait` для Nested Set)
   - [`Series`](app/Modules/Catalog/Infrastructure/Models/Series.php:14), [`Equivalent`](app/Modules/Catalog/Infrastructure/Models/Equivalent.php:17)

### 5.2 Domain Entity

- [`GroupEntity`](app/Modules/Catalog/Domain/Entities/GroupEntity.php:10) — `name`, `slug`, `description`, `published`, `meta`
- [`CategoryEntity`](app/Modules/Catalog/Domain/Entities/CategoryEntity.php:8) — + `parentId`, `left/right/depth`, `children`, `svgIcon`
- [`SeriesEntity`](app/Modules/Catalog/Domain/Entities/SeriesEntity.php:7) — `name`, `nameRu`
- [`EquivalentEntity`](app/Modules/Catalog/Domain/Entities/EquivalentEntity.php:7) — `name`, `categoryId`

> ⚠️ Согласно [`INSTRUCTION.md`](resources/docs/INSTRUCTION.md:44), сущность **не содержит** вычисляемых
> агрегатов (`quantity` и т.п.). Кол-во товаров считается отдельно в Query (см. раздел 3).

---

## 6. Вкладки и связи с товарами

### 6.1 Вкладка «Товары»

Паттерн одинаков для Category/Equivalent/Group ([`Group/Panels/Products.vue`](resources/js/Pages/Catalog/Group/Panels/Products.vue:1)):

```vue
<SearchAddProduct  :route="route('admin.catalog.group.products.attach', { id: groupId })" />
<SearchAddProducts :route="route('admin.catalog.group.products.attach', { id: groupId })" />
<TableRelation entity="group" :id="groupId" />
```

- [`SearchAddProduct`](resources/js/VueComponents/Search/AddProduct.vue) / [`SearchAddProducts`](resources/js/VueComponents/Search/AddProducts.vue) — добавление одного/нескольких товаров.
- [`TableRelation`](resources/js/VueComponents/Product/TableRelation.vue:40) — таблица товаров, данные грузит AJAX-ом:
  ```js
  axios.get(route(`admin.catalog.${props.entity}.products`, { id: props.id, page }))
  ```
  и подтягивает картинки через [`admin.photo.get-by-ids`](resources/js/VueComponents/Product/TableRelation.vue:53).

Маршруты связи (pivot) заданы в [`routes/web.php`](app/Modules/Catalog/routes/web.php:119):

| Сущность | Контроллер | Маршруты |
|----------|-----------|----------|
| Category | [`CategoryProductController`](app/Modules/Catalog/Presentation/Http/Controllers/Web/CategoryProductController.php) | `products` / `products.sync` / `products.attach` / `products.detach` |
| Equivalent | [`EquivalentController`](app/Modules/Catalog/Presentation/Http/Controllers/Web/EquivalentController.php:91) | `products` / `products.sync` / `products.attach` / `products.detach` |
| Group | [`GroupProductController`](app/Modules/Catalog/Presentation/Http/Controllers/Web/GroupProductController.php) | `products` / `products.sync` / `products.attach` / `products.detach` |

Соответствующие UseCase-классы:
- `CategoryProduct`: [`AttachProductsToCategoryUseCase`](app/Modules/Catalog/Application/Actions/CategoryProduct/AttachProductsToCategoryUseCase.php), `AssignProductsToCategoryUseCase`, `DetachProductsFromCategoryUseCase`
- `EquivalentProduct`: [`AttachProductsToEquivalentUseCase`](app/Modules/Catalog/Application/Actions/EquivalentProduct/AttachProductsToEquivalentUseCase.php) и др.
- `GroupProduct`: [`AttachProductsToGroupUseCase`](app/Modules/Catalog/Application/Actions/GroupProduct/AttachProductsToGroupUseCase.php) и др.

### 6.2 Серии — связь через `series_id` (не pivot)

У `Series` связь иная: колонка `series_id` в таблице `products` (модель [`Series::products()`](app/Modules/Catalog/Infrastructure/Models/Series.php:32) — `hasMany`).
Реализация в [`SeriesRepository`](app/Modules/Catalog/Infrastructure/Persistence/SeriesRepository.php:115):

- `attachProducts()` → `Product::whereIn('id', $new)->update(['series_id' => $seriesId])`
- `detachProduct()` → `$product->series_id = null`

Страница [`Series/Show`](resources/js/Pages/Catalog/Series/Show.vue:1) — упрощённый вариант карточки без `Block/Info`,
товары добавляются через [`SearchAddProduct`](resources/js/VueComponents/Search/AddProduct.vue) на маршруты
`admin.catalog.series.add-product` / `add-products`, удаление — `del-product` ([`SeriesController::add_product()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/SeriesController.php:86)).

### 6.3 Другие вкладки

- **Подкатегории** — [`Category/Panels/Children.vue`](resources/js/Pages/Catalog/Category/Panels/Children.vue:1):
  popover создания + [`CategoryChildren`](resources/js/VueComponents/Category/Children.vue) → рекурсивный [`CategoryRow`](resources/js/VueComponents/Category/Row.vue).
  Создание: [`router.post(route('admin.catalog.category.store', form))`](resources/js/Pages/Catalog/Category/Panels/Children.vue:41) с `parent_id`.
- **Атрибуты** — [`Category/Panels/Attributes.vue`](resources/js/Pages/Catalog/Category/Panels/Attributes.vue:88):
  AJAX `admin.catalog.category.attributes` возвращает `{ parent: [...], self: [...] }`.
- **Контент блоки** — [`Category/Panels/Blocks.vue`](resources/js/Pages/Catalog/Category/Panels/Blocks.vue:1) и
  [`Group/Panels/Blocks.vue`](resources/js/Pages/Catalog/Group/Panels/Blocks.vue:1) рендерят [`ContentBlockEditor`](resources/js/VueComponents/Content/ContentBlock/ContentBlockEditor.vue)
  с `container-type` (`category` / `group`).

---

## 7. Изображения

### 7.1 Компонент [`PhotoDTO`](resources/js/VueComponents/PhotoDTO.vue:1)

Используется в `Block/Info.vue` для картинки/иконки:

```vue
<PhotoDTO model-type="catalog.group" :entity-id="group.id" type="image" />
<PhotoDTO model-type="catalog.category" :entity-id="category.id" type="image" />
```

Ключевые пропсы: `model-type` (`{модуль}.{сущность}`, например `catalog.category`), `entity-id`, `type` (`image` / `icon` / `gallery`).

Поток данных:
1. **Загрузка** — [`loadImages()`](resources/js/VueComponents/PhotoDTO.vue:110) → GET [`admin.photo.get-by-entity`](app/Modules/Shared/routes/web.php:14).
2. **Сохранение файла** — [`onFileChange()`](resources/js/VueComponents/PhotoDTO.vue:145) → POST [`admin.photo.upload`](app/Modules/Shared/routes/web.php:22) с `FormData` (`file`, `imageableId`, `modelType`, `type`).
3. **Метаданные (alt/title/description)** — [`onSubmitData()`](resources/js/VueComponents/PhotoDTO.vue:217) → POST [`admin.photo.save-data`](app/Modules/Shared/routes/web.php:20).
4. **Сортировка (gallery)** — POST [`admin.photo.sort`](app/Modules/Shared/routes/web.php:24).
5. **Удаление** — DELETE [`admin.photo.destroy`](app/Modules/Shared/routes/web.php:26).

### 7.2 Backend фото

Контроллер [`PhotoController`](app/Modules/Shared/Presentation/Http/Controllers/Web/PhotoController.php:28) — тонкий,
делегирует в `UseCase` (`UploadPhotoUseCase`, `GetPhotoByEntityUseCase`, `SavePhotoDataUseCase` и др.).

Модель [`Photo`](app/Modules/Shared/Infrastructure/Models/Photo.php:35) — полиморфная (`morphTo imageable`),
путь строится из `model_type` по схеме `/{module}/{model}/{imageable_id}/` ([`patternGeneratePath()`](app/Modules/Shared/Infrastructure/Models/Photo.php:69)).

Связь на стороне сущности обеспечивают трейты:
- [`ImageField`](app/Modules/Base/Traits/ImageField.php:10) — `morphOne(Photo, 'imageable')->where('type', 'image')` (используют [`Category`](app/Modules/Catalog/Infrastructure/Models/Category.php:34), [`Group`](app/Modules/Catalog/Infrastructure/Models/Group.php:22))
- [`IconField`](app/Modules/Base/Traits/IconField.php) — иконки
- [`PhotoField`](app/Modules/Base/Traits/PhotoField.php) — галереи

---

## 8. Маршруты (краткий справочник)

Все маршруты каталога объявлены в [`app/Modules/Catalog/routes/web.php`](app/Modules/Catalog/routes/web.php:21)
(группа `role:admin|staff`, префикс `admin/catalog`, имя `admin.catalog.`).

CRUD-ресурсы:

| Ресурс | Объявление | Параметр |
|--------|-----------|----------|
| Category | [`Route::resource('category', ...)`](app/Modules/Catalog/routes/web.php:76) | `id` |
| Equivalent | [`Route::resource('equivalent', ...)`](app/Modules/Catalog/routes/web.php:192) | `id` |
| Group | [`Route::resource('group', ...)->except(['create','edit'])`](app/Modules/Catalog/routes/web.php:193) | `id` |
| Series | [`Route::resource('series', ...)->except(['create','edit'])`](app/Modules/Catalog/routes/web.php:195) | `id` |

Дополнительные (используются во вкладках):

| Имя маршрута | Метод | Контроллер/метод |
|--------------|-------|------------------|
| `category.tree` | GET | [`CategoryController::tree()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/CategoryController.php:122) |
| `category.attributes` | GET | [`CategoryController::attributes()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/CategoryController.php:138) |
| `category.products` | GET | [`CategoryController::products()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/CategoryController.php:129) |
| `group.list` | GET | [`GroupController::list()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/GroupController.php:88) |
| `series.list` | GET | [`SeriesController::list()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/SeriesController.php:117) |
| `series.add-product` | POST | [`SeriesController::add_product()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/SeriesController.php:86) |
| `series.add-products` | POST | [`SeriesController::add_products()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/SeriesController.php:94) |
| `series.del-product` | DELETE | [`SeriesController::del_product()`](app/Modules/Catalog/Presentation/Http/Controllers/Web/SeriesController.php:109) |

Фото-маршруты — в [`app/Modules/Shared/routes/web.php`](app/Modules/Shared/routes/web.php:6), имя `admin.photo.*`.

---

## 9. Шпаргалка «как добавить новую сущность по этому образцу»

1. **Domain**: `Entity` + `RepositoryInterface` в [`app/Modules/Catalog/Domain`](app/Modules/Catalog/Domain).
2. **Application**: `DTO` (Spatie Data) + `Query`/`UseCase` в [`app/Modules/Catalog/Application`](app/Modules/Catalog/Application).
3. **Infrastructure**: Eloquent-модель + `Repository` (hydrate/save) в [`app/Modules/Catalog/Infrastructure`](app/Modules/Catalog/Infrastructure).
4. **Presentation**: контроллер в [`app/Modules/Catalog/Presentation/Http/Controllers/Web`](app/Modules/Catalog/Presentation/Http/Controllers/Web)
   + маршруты в [`routes/web.php`](app/Modules/Catalog/routes/web.php).
5. **Frontend**: `Index.vue` / `Show.vue` + `Block/Info.vue` + `Panels/*.vue` в
   [`resources/js/Pages/Catalog`](resources/js/Pages/Catalog).

### Типовые «куски» для копирования

- **Index**: фильтр + popover создания + таблица + `Pagination` → [`Group/Index`](resources/js/Pages/Catalog/Group/Index.vue:1).
- **Show с формой**: `hasChanges` + `router.visit(..., { method: 'put' })` → [`Group/Block/Info.vue`](resources/js/Pages/Catalog/Group/Block/Info.vue:35).
- **Вкладка товаров**: `SearchAddProduct` + `TableRelation` → [`Group/Panels/Products.vue`](resources/js/Pages/Catalog/Group/Panels/Products.vue:1).
- **Изображение**: `PhotoDTO` → [`Category/Block/Info.vue`](resources/js/Pages/Catalog/Category/Block/Info.vue:5).

---

## 10. Замеченные особенности/потенциальные проблемы

- В [`Category/Panels/Blocks.vue`](resources/js/Pages/Catalog/Category/Panels/Blocks.vue:12) проп передан как
  `:container-id="categoryd"` — опечатка; по образцу [`Group/Panels/Blocks.vue`](resources/js/Pages/Catalog/Group/Panels/Blocks.vue:12)
  должно быть `:container-id="categoryId"`.
- `Category` и `Group` используют **разные** подходы к Meta/SEO: у Category meta хранится и обновляется,
  у Group в `Block/Info.vue` поля meta не редактируются.
- Сохранение `Category` выполняется с `preserveState: false` (страница перезагружается с сервера), а
  `Group`/`Equivalent` — с `preserveState: true` + ручным обновлением эталона `initialInfo` в `onSuccess`.
