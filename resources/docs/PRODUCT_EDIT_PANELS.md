# Панели редактирования товара (Catalog / Product / Edit)

Панель — это вкладка (`<el-tab-pane>`) на странице [`Edit.vue`](resources/js/Pages/Catalog/Product/Edit.vue).
Каждая панель **сама загружает и сохраняет свои данные** через AJAX-эндпоинты.
Родитель [`Edit.vue`](resources/js/Pages/Catalog/Product/Edit.vue) передаёт панели **только `productId`**
(и служебный флаг `active`), НЕ передаёт объект `product` и НЕ передаёт `errors`.

Эталонная (уже реализованная) панель — [`Common.vue`](resources/js/Pages/Catalog/Product/Panels/Common.vue)
(«Общие параметры»). Все остальные панели делаются по тому же шаблону.

---

## 1. Общая схема

```
Browser (Vue)
   │  GET  /admin/catalog/product/edit/{panel}/{id}     → Query → View*Data (JSON)
   │  POST /admin/catalog/product/edit/{panel}/{id}     → UseCase (DTO-валидация) → View*Data (JSON)
   ▼
Controller (тонкий): validateAndCreate(DTO) → UseCase->execute() → Query->execute()
```

- **GET** (`loadXxx`) — отдаёт данные для заполнения формы.
- **POST** (`saveXxx`) — принимает `Request`, валидирует через `Update*Data::validateAndCreate()`,
  вызывает UseCase и возвращает актуальные `View*Data` (JSON) — фронт обновляет форму из ответа.

> Правило: валидацию входящих данных делает **DTO**, а не FormRequest.
> Контроллер использует стандартный `Illuminate\Http\Request`.

---

## 2. Backend-слой панели

Для каждой панели создаются (в `app/Modules/Catalog/`):

| Что | Где | Комментарий |
|-----|-----|-------------|
| DTO вывода | `Application/DTOs/Product/EditPage/View{Panel}ProductData.php` | обычный класс, поля camelCase, статический `fromEntity()` |
| DTO ввода | `Application/DTOs/Product/EditPage/Update{Panel}ProductData.php` | `extends Data`, поля camelCase + атрибуты валидации |
| Query | `Application/Actions/Product/EditPage/Get{Panel}ProductQuery.php` | `readonly`, `execute(int $id)` → `View*Data` |
| UseCase | `Application/Actions/Product/EditPage/Set{Panel}ProductUseCase.php` | `readonly`, `execute(int $id, Update*Data $dto)` |
| Методы контроллера | `Presentation/Http/Controllers/Web/ProductEditController.php` | `load{Panel}` / `save{Panel}` |
| Маршруты | `routes/web.php` | `GET` и `POST` на один и тот же URL |

### 2.1 View-DTO (вывод)

Обычный класс (не `Data` — валидация для вывода не нужна). Обязательно поле `id`.
Все поля — camelCase. Маппинг из сущности — статический метод `fromEntity()`.

```php
class ViewDescriptionProductData
{
    public function __construct(
        public int $id,
        public string $description,
        public string $short,
        // ...
    ) {}

    public static function fromEntity(ProductEntity $entity): self
    {
        return new self(
            id: $entity->id,
            description: $entity->description,
            short: $entity->short,
            // ...
        );
    }
}
```

Если панели нужны связи «многие-ко-многим» (категории/комнаты), их ID получаем через
pivot-репозитории и передаём в `fromEntity()` как отдельные аргументы (см. [`GetCommonProductQuery`](app/Modules/Catalog/Application/Actions/Product/EditPage/GetCommonProductQuery.php:24)).

### 2.2 Update-DTO (ввод, с валидацией)

`extends Spatie\LaravelData\Data`. Поля camelCase, обязательно присутствуют все поля,
которые отправляет форма. Валидационные правила — те же, что были в легаси FormRequest.

```php
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;

class UpdateDescriptionProductData extends Data
{
    public function __construct(
        #[Required, Numeric]
        public readonly int $id,
        #[Nullable, StringType]
        public readonly ?string $description = null,
        // ...
    ) {}

    public static function messages(): array
    {
        return [
            'description.required' => 'Введите описание',
        ];
    }
}
```

Уникальность с исключением текущего товара (параметр маршрута `{id}`):

```php
#[Unique('products', 'name', ignore: new RouteParameterReference('id'))]
public readonly string $name,
```

### 2.3 Query

```php
final readonly class GetDescriptionProductQuery
{
    public function __construct(private ProductRepositoryInterface $productRepository) {}

    public function execute(int $id): ViewDescriptionProductData
    {
        return ViewDescriptionProductData::fromEntity(
            $this->productRepository->getById($id)
        );
    }
}
```

### 2.4 UseCase

```php
readonly class SetDescriptionProductUseCase
{
    public function __construct(private ProductRepositoryInterface $productRepository) {}

    public function execute(int $id, UpdateDescriptionProductData $dto): ProductEntity
    {
        $product = $this->productRepository->getById($id);
        $product->description = trim($dto->description ?? '');
        // ...
        return $this->productRepository->save($product);
    }
}
```

### 2.5 Контроллер и маршруты

```php
// ProductEditController.php
public function loadDescription(int $id)
{
    return response()->json($this->getDescriptionProductQuery->execute($id));
}

public function saveDescription(Request $request, int $id)
{
    $dto = UpdateDescriptionProductData::validateAndCreate($request->all());
    $this->setDescriptionProductUseCase->execute($id, $dto);
    return response()->json($this->getDescriptionProductQuery->execute($id));
}
```

```php
// routes/web.php (внутри группы product/edit)
Route::get('/description/{id}', [ProductEditController::class, 'loadDescription'])->name('description');
Route::post('/description/{id}', [ProductEditController::class, 'saveDescription'])->name('description');
```

> Один URL на `GET` и `POST`, имя маршрута одно: `admin.catalog.product.edit.{panel}`.

---

## 3. Frontend-слой панели

### 3.1 Компонент панели

Корень компонента — `<el-tab-pane :name="name">`. Панель получает только:

```ts
const props = defineProps({
    productId: { type: Number, required: true },
    active:    { type: Boolean, default: false }, // активна ли вкладка
    name:      { type: String,  default: 'description' }, // ключ вкладки
})
```

Шаблон обязателен:

```html
<el-tab-pane :name="name">
    <template #label>
        <span class="custom-tabs-label">
            <i class="fa-light fa-..."></i>
            <span> Название панели</span>
        </span>
    </template>

    <!-- пока данные не загружены — показываем спиннер, а не пустые поля -->
    <div v-if="loading || !loaded" class="py-6 text-center text-gray-400">
        <i class="fa-light fa-spinner fa-spin"></i> Загрузка...
    </div>

    <div v-else>
        ...поля формы...
    </div>
</el-tab-pane>
```

Форма — `reactive` объект с camelCase-полями (как в View-DTO). Поля заполняются
из ответа `GET`-эндпоинта, а не из props.

Автозагрузка при активации — через `watch(() => props.active, ..., { immediate: true })`
и композабл [`useProductPanel`](resources/js/Pages/Catalog/Product/Panels/useProductPanel.js).

### 3.2 Композабл useProductPanel (кэш 5 минут)

```js
import { useProductPanel } from './useProductPanel'

const { load, loading, loaded, setCache } = useProductPanel('description', async () => {
    return api.get(
        route('admin.catalog.product.edit.description', { id: props.productId }),
        null,
        { showSuccess: false },
    )
})

watch(() => props.active, async (active) => {
    if (!active) return
    const { data, fromCache } = await load()
    // перезатираем форму только если данные реально перезагрузились
    if (!fromCache && data) applyData(data)
}, { immediate: true })
```

Логика кэша (уже реализована в [`useProductPanel.js`](resources/js/Pages/Catalog/Product/Panels/useProductPanel.js)):

- `load()` возвращает `{ data, fromCache }`.
- Если данные панели загружались и прошло **меньше 5 минут** — сетевой запрос не выполняется,
  возвращается кэш (`fromCache: true`).
- Если кэша нет или прошло больше 5 минут — выполняется запрос (`fromCache: false`).

Пока данные ещё не получены (`loading || !loaded`), вместо формы показывается
индикатор загрузки. Поля остаются пустыми только в этот короткий промежуток
и не «моргают» при повторном входе на панель (данные берутся из кэша).

### 3.3 Сохранение (один эндпоинт)

Все поля панели (включая связи many-to-many) сохраняются **одним** POST-запросом:

```js
function onSave() {
    isSaving.value = true
    Object.keys(saveErrors).forEach(k => delete saveErrors[k])

    api.post(
        route('admin.catalog.product.edit.description', { id: props.productId }),
        { ...form },
    ).then(data => {
        applyData(data)   // обновляем форму из ответа (slug и т.п.)
        setCache(data)    // обновляем кэш панели
    }).catch(error => {
        const errs = error?.response?.data?.errors
        if (errs && typeof errs === 'object') {
            for (const [key, value] of Object.entries(errs)) {
                saveErrors[key] = Array.isArray(value) ? value[0] : value
            }
        }
    }).finally(() => {
        isSaving.value = false
    })
}
```

Ошибки валидации приходят в `error.response.data.errors` (ключи — camelCase, как поля DTO)
и выводятся в `{{ errors.field }}`.

---

## 4. Активная вкладка и `?panel=`

[`Edit.vue`](resources/js/Pages/Catalog/Product/Edit.vue) управляет активной вкладкой:

- `<el-tabs v-model="activePanel">`, каждой панели передаётся `:active="activePanel === 'key'"`.
- При открытии страницы с `?panel=key` — эта панель сразу становится активной.
- При переключении вкладки в URL дописывается `?panel=key` (через `history.replaceState`,
  без перезагрузки страницы).

Поэтому у **каждой** панели `name` (ключ вкладки) должен совпадать с ключом в `activePanel`
и значением `?panel=`.

---

## 5. Как добавить новую панель (чек-лист)

1. **View-DTO** `View{Panel}ProductData` + `fromEntity()`.
2. **Update-DTO** `Update{Panel}ProductData` с валидацией (правила — из легаси FormRequest/сервиса).
3. **Query** `Get{Panel}ProductQuery` (`execute(int $id)`).
4. **UseCase** `Set{Panel}ProductUseCase` (`execute(int $id, Update*Data $dto)`).
   - Если панель поддерживает «для всех товаров модификации» — добавить флаг `modification`
     и использовать [`GetModificationByProductQuery`](app/Modules/Catalog/Application/Actions/Modification/GetModificationByProductQuery.php)
     для получения всех товаров модификации (см. раздел 6).
5. **Контроллер**: методы `load{Panel}(int $id)` и `save{Panel}(Request $request, int $id)`.
6. **Маршруты**: `GET` + `POST` `/edit/{panel}/{id}` с именем `...edit.{panel}`.
7. **Vue-панель** `Panels/{Panel}.vue` по образцу [`Common.vue`](resources/js/Pages/Catalog/Product/Panels/Common.vue):
   - `defineProps({ productId, active, name })`;
   - корень `<el-tab-pane :name="name">`;
   - автозагрузка через `useProductPanel` + `watch(active)`;
   - сохранение одним POST.
8. **Регистрация** в [`Edit.vue`](resources/js/Pages/Catalog/Product/Edit.vue):
   ```html
   <PanelDescription :product-id="product.id" :active="activePanel === 'description'" />
   ```
   и импорт компонента. В `activePanel` панель попадает автоматически через `name`.

---

## 6. Распространение на модификацию

Если у панели есть чекбокс «Сохранять для всех товаров из Модификации»:

- DTO ввода содержит `#[Nullable, BooleanType] ?bool $modification`.
- UseCase использует [`GetModificationByProductQuery`](app/Modules/Catalog/Application/Actions/Modification/GetModificationByProductQuery.php),
  который по `productId` одного из товаров возвращает **все товары модификации**
  (`ModificationViewData.products`: `productId`, `values` — названия вариантов).
- Для каждого товара из списка формируется суффикс названия
  `' ' . implode(' ', $values)` (как в легаси `ProductService::editCommon`),
  и **все** поля панели (включая связи many-to-many) применяются к каждому товару.

См. эталонную реализацию в [`SetCommonProductUseCase`](app/Modules/Catalog/Application/Actions/Product/EditPage/SetCommonProductUseCase.php:40).

---

## 7. Реализованные файлы (панель Common)

Backend:

- [`ViewCommonProductData`](app/Modules/Catalog/Application/DTOs/Product/EditPage/ViewCommonProductData.php)
- [`UpdateCommonProductData`](app/Modules/Catalog/Application/DTOs/Product/EditPage/UpdateCommonProductData.php)
- [`GetCommonProductQuery`](app/Modules/Catalog/Application/Actions/Product/EditPage/GetCommonProductQuery.php)
- [`SetCommonProductUseCase`](app/Modules/Catalog/Application/Actions/Product/EditPage/SetCommonProductUseCase.php)
- [`GetModificationByProductQuery`](app/Modules/Catalog/Application/Actions/Modification/GetModificationByProductQuery.php)
- [`ProductEditController`](app/Modules/Catalog/Presentation/Http/Controllers/Web/ProductEditController.php)

Frontend:

- [`Edit.vue`](resources/js/Pages/Catalog/Product/Edit.vue)
- [`Panels/Common.vue`](resources/js/Pages/Catalog/Product/Panels/Common.vue)
- [`Panels/useProductPanel.js`](resources/js/Pages/Catalog/Product/Panels/useProductPanel.js)
