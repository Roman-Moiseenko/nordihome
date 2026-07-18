# Архитектура: вложенные экземпляры виджетов (widget-in-widget)

## 1. Текущее состояние и проблема

### Как работают виджеты сейчас

1. **Widget** — тип/шаблон виджета. Содержит:
   - `name`, `slug`, `category`, `schema` (JSON Schema с полями)
   - Blade-шаблон `widgets/{category}/{slug}.blade.php`

2. **WidgetInstance** (экземпляр) — конкретный экземпляр виджета с параметрами:
   - `widget_id` → привязка к Widget
   - `params` → значения полей (JSON)
   - Может быть привязан к ContentBlock через `content_block_id`

3. **ContentBlock** — блок на странице:
   - Содержит один WidgetInstance
   - Имеет `sort`, `section`, `caption`, `active`

4. **SchemaEditor** — редактор схемы для Widget:
   - Позволяет добавить поле типа `widget` (integer + format=widget)
   - На фронте `WidgetFieldRenderer` уже умеет отображать поле `format=widget` как тег с ID виджета и кнопкой "Выбрать виджет"

5. **GetWidgetInstanceFormUseCase** — строит форму для экземпляра:
   - Парсит schema → WidgetFormFieldData[]
   - Значения берёт из `instance.params`
   - Для `format='product'` обогащает данные через `ProductSearchService`
   - Для `format='widget'` **пока не делает ничего**, просто передаёт `value` как `integer` ID

### Проблема

Поле типа `widget` сейчас хранит только **ID виджета**, но не ID экземпляра. Т.е. непонятно, какой именно экземпляр использовать. Нам нужно:

1. Хранить **не ID виджета**, а **ID другого WidgetInstance**
2. При рендере формы — показывать название экземпляра и возможность его редактировать
3. При удалении родительского экземпляра — каскадно удалять или отвязывать дочерний

---

## 2. Архитектура решения

### 2.1 Изменение формата поля

В схеме виджета появляется новый тип поля:

```json
{
  "type": "integer",
  "format": "widget_instance"
}
```

Отличие от `format=widget`:
- `format=widget` — хранит ID типа виджета (Widget.id). При рендере на сайте используется схема этого виджета
- `format=widget_instance` — хранит ID экземпляра (WidgetInstance.id). При рендере на сайте берётся готовый экземпляр с его параметрами

### 2.2 Хранение в params

В `params` экземпляра-родителя поле хранит ID дочернего экземпляра:

```php
$params = [
    'my_widget_field' => 15,  // WidgetInstance.id = 15
]
```

### 2.3 Бекенд: задействованные классы

| Класс | Роль | Изменения |
|-------|------|-----------|
| `WidgetFormFieldData` | DTO поля формы | Без изменений — `format: 'widget_instance'` уже поддерживается через `?string $format` |
| `GetWidgetInstanceFormUseCase` | Строит форму | **Добавить** обработку `format='widget_instance'` — подгружать `WidgetInstanceEntity` по ID и передавать его название в `value` как объект `{ id, title, widgetName }` |
| `WidgetInstanceController` | API | **Добавить** метод `byWidget(int $widgetId)` — получение списка экземпляров по типу виджета |
| `GetWidgetInstanceFormUseCase::enrichWidgetInstanceValue()` | **Новый метод** | Аналог `enrichProductValue()`, подгружает дочерний экземпляр и возвращает его данные |
| `GetWidgetInstanceFormUseCase::getInstancesByWidgetId()` | **Новый метод** | Возвращает экземпляры конкретного виджета для списка выбора |
| `WidgetInstanceRepository` | Репозиторий | Без изменений (уже есть `getById()` и `getByWidgetId()`) |
| `RemoveWidgetInstanceUseCase` | Удаление экземпляра | **Добавить** поиск ссылающихся экземпляров и очистку ссылок на удаляемый id |
| `WidgetRendererService` | **Новый класс** | Рендер дочернего экземпляра на сайте (с Blade-директивой) |

### 2.4 Фронтенд: компоненты

| Компонент | Роль | Изменения |
|-----------|------|-----------|
| `WidgetFieldRenderer.vue` | Рендер полей формы | **Добавить** отрисовку `format='widget_instance'` как тег с названием экземпляра и кнопками "Выбрать/Изменить/Удалить". **Добавить** метод `setFieldValue()` для внешнего обновления |
| `WidgetInstanceSelectorDialog.vue` | **Новый** | Диалог выбора экземпляра: категория виджета → выбор типа виджета → выбор конкретного экземпляра |
| `SchemaEditor.vue` | Редактор схемы | **Добавить** в выпадающий список тип `"Экземпляр виджета (widget_instance)"` |
| `ContentBlockItem.vue` | Блок контента | **Добавить** обработку события `select-nested-widget-instance` — открытие диалога выбора экземпляра |

---

## 3. Пошаговый план реализации

### Шаг 1: SchemaEditor — добавить тип поля

**Файл:** `resources/js/Pages/Content/Widget/Elements/SchemaEditor.vue`

В список `el-option` в диалоге добавления поля добавить:

```diff
<el-option label="Массив чисел (array + integer)" value="array_integers" />
+ <el-option label="Экземпляр виджета (widget_instance)" value="widget_instance" />
```

В методе `addProperty()` добавить case:

```ts
case 'widget_instance':
    propConfig.type = 'integer'
    propConfig.format = 'widget_instance'
    break
```

### Шаг 2: GetWidgetInstanceFormUseCase — enrichWidgetInstanceValue

**Файл:** `app/Modules/Content/Application/Actions/WidgetInstance/GetWidgetInstanceFormUseCase.php`

#### 2.1 Добавить метод `enrichWidgetInstanceValue()`

```php
/**
 * Обогащает значение поля экземпляра виджета: если есть id, подгружает данные.
 *
 * @param mixed $value
 * @return array|null
 */
private function enrichWidgetInstanceValue(mixed $value): array|null
{
    $instanceId = null;

    if (is_int($value) || (is_string($value) && is_numeric($value))) {
        $instanceId = (int) $value;
    } elseif (is_array($value) && isset($value['id'])) {
        $instanceId = (int) $value['id'];
    }

    if ($instanceId) {
        try {
            $instance = $this->instanceRepository->getById($instanceId);
            return [
                'id' => $instance->id,
                'title' => $instance->title,
                'widgetName' => $instance->widgetName,
                'widgetId' => $instance->widgetId,
            ];
        } catch (\Exception) {
            return [
                'id' => $instanceId,
                'title' => null,
                'widgetName' => null,
                'widgetId' => null,
            ];
        }
    }

    return null;
}
```

#### 2.2 В `buildFields()` добавить обработку

Внутри цикла `foreach ($properties as $name => $prop)` после обработки `format='product'` добавить:

```php
// Для format: 'widget_instance' — подгружаем данные экземпляра виджета
if (($prop['format'] ?? null) === 'widget_instance') {
    $currentValue = $this->enrichWidgetInstanceValue($currentValue);
}
```

#### 2.3 Добавить метод `getInstancesByWidgetId()`

```php
/**
 * Возвращает базовые данные о всех экземплярах виджета.
 *
 * @param int $widgetId
 * @return array<int, array{id: int, title: ?string, widgetName: string}>
 */
public function getInstancesByWidgetId(int $widgetId): array
{
    $instances = $this->instanceRepository->getByWidgetId($widgetId);
    return array_map(fn($inst) => [
        'id' => $inst->id,
        'title' => $inst->title,
        'widgetName' => $inst->widgetName,
    ], $instances);
}
```

### Шаг 3: WidgetInstanceController — API метод

**Файл:** `app/Modules/Content/Presentation/Http/Controllers/Web/WidgetInstanceController.php`

Добавить метод:

```php
/**
 * Получить экземпляры Widget по widget_id.
 * GET /admin/content/widget-instances/by-widget/{widgetId}
 */
public function byWidget(int $widgetId): JsonResponse
{
    $instances = $this->getWidgetInstanceFormUseCase->getInstancesByWidgetId($widgetId);
    return response()->json($instances);
}
```

Добавить маршрут в `routes/web.php`:

```php
Route::get('widget-instances/by-widget/{widgetId}', [WidgetInstanceController::class, 'byWidget'])
    ->name('admin.content.widget-instances.by-widget');
```

### Шаг 4: WidgetFieldRenderer — отрисовка поля widget_instance

**Файл:** `resources/js/VueComponents/Content/ContentBlock/WidgetFieldRenderer.vue`

#### 4.1 В template, в секцию compact-fields добавить:

```vue
<!-- widget_instance -->
<div v-else-if="field.format === 'widget_instance'" class="nested-widget-instance-field">
    <template v-if="formModel[field.name]?.id">
        <el-tag type="success" closable @close="removeNestedWidgetInstance(field.name)">
            {{ formModel[field.name].widgetName || 'Виджет' }}
            <template v-if="formModel[field.name].title">
                — {{ formModel[field.name].title }}
            </template>
        </el-tag>
        <el-button size="small" @click="openNestedWidgetInstanceSelector(field.name)">
            Изменить
        </el-button>
    </template>
    <el-button v-else size="small" @click="openNestedWidgetInstanceSelector(field.name)">
        Выбрать экземпляр виджета
    </el-button>
</div>
```

#### 4.2 В emit добавить новое событие:

```ts
const emit = defineEmits<{
    (e: 'save', params: Record<string, any>): void
    (e: 'select-nested-widget', fieldName: string): void
    (e: 'select-nested-widget-instance', fieldName: string): void  // <-- новое
}>()
```

#### 4.3 Добавить методы:

```ts
function removeNestedWidgetInstance(fieldName: string) {
    formModel[fieldName] = null
}

function openNestedWidgetInstanceSelector(fieldName: string) {
    emit('select-nested-widget-instance', fieldName)
}
```

#### 4.4 Добавить метод setFieldValue и expose:

```ts
/** Программно установить значение поля (для внешних вызовов) */
function setFieldValue(name: string, value: any) {
    formModel[name] = value
}

defineExpose({ setFieldValue })
```

> **Важно:** Поле `widget_instance` должно находиться в `compactFields`. Текущая логика `compactFields` уже включает поля с `format` (кроме `html`), поэтому `widget_instance` туда попадёт автоматически. Дополнительных изменений в computed не требуется.

### Шаг 5: WidgetInstanceSelectorDialog — новый компонент

**Файл:** `resources/js/VueComponents/Content/ContentBlock/WidgetInstanceSelectorDialog.vue`

```vue
<template>
    <el-dialog
        :model-value="visible"
        @update:model-value="$emit('close')"
        title="Выберите экземпляр виджета"
        width="600px"
    >
        <!-- Шаг 1: выбор типа виджета -->
        <template v-if="step === 'select-widget'">
            <el-tabs tab-position="left" class="min-h-[300px]">
                <el-tab-pane
                    v-for="(group, key) in widgetGroups"
                    :key="key"
                    :label="group.label"
                >
                    <div class="space-y-2">
                        <div
                            v-for="widget in group.widgets"
                            :key="widget.id"
                            class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                            @click="selectWidget(widget)"
                        >
                            <div class="font-medium">{{ widget.name }}</div>
                            <div class="text-xs text-gray-500">{{ widget.description }}</div>
                        </div>
                        <el-empty v-if="group.widgets.length === 0" description="Нет виджетов" />
                    </div>
                </el-tab-pane>
            </el-tabs>
        </template>

        <!-- Шаг 2: выбор экземпляра -->
        <template v-else-if="step === 'select-instance'">
            <div class="mb-3">
                <el-button size="small" @click="step = 'select-widget'">
                    ← Назад к виджетам
                </el-button>
                <span class="ml-2 font-medium">{{ selectedWidget?.name }}</span>
            </div>

            <div v-if="loading" class="text-center py-4">
                <el-icon class="is-loading"><i class="fa-light fa-spinner" /></el-icon>
                Загрузка экземпляров...
            </div>

            <div v-else-if="instances.length === 0" class="text-center py-4 text-gray-400">
                Нет экземпляров этого виджета
            </div>

            <div v-else class="space-y-2">
                <div
                    v-for="inst in instances"
                    :key="inst.id"
                    class="p-3 border rounded-lg hover:bg-gray-50 cursor-pointer"
                    @click="selectInstance(inst)"
                >
                    <div class="font-medium">{{ inst.title || `Экземпляр #${inst.id}` }}</div>
                    <div class="text-xs text-gray-500">ID: {{ inst.id }}</div>
                </div>
            </div>
        </template>

        <template #footer>
            <el-button @click="$emit('close')">Отмена</el-button>
        </template>
    </el-dialog>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useContentStore } from '@Res/contentStore'
import api from '@Res/api'
import { route } from 'ziggy-js'

const props = defineProps<{ visible: boolean }>()
const emit = defineEmits<{
    (e: 'close'): void
    (e: 'select', instance: any): void
}>()

const store = useContentStore()
const widgetGroups = computed(() => store.widgets as Record<string, { key: string; label: string; widgets: any[] }>)

const step = ref<'select-widget' | 'select-instance'>('select-widget')
const selectedWidget = ref<any>(null)
const instances = ref<any[]>([])
const loading = ref(false)

async function selectWidget(widget: any) {
    selectedWidget.value = widget
    step.value = 'select-instance'
    loading.value = true
    try {
        const res = await api.get(route('admin.content.widget-instances.by-widget', { widgetId: widget.id }))
        instances.value = Array.isArray(res) ? res : []
    } catch {
        instances.value = []
    } finally {
        loading.value = false
    }
}

function selectInstance(instance: any) {
    emit('select', instance)
}
</script>
```

### Шаг 6: ContentBlockItem — интеграция диалога

**Файл:** `resources/js/VueComponents/Content/ContentBlock/ContentBlockItem.vue`

#### 6.1 Добавить импорт и состояние:

```ts
import WidgetInstanceSelectorDialog from './WidgetInstanceSelectorDialog.vue'

const fieldRendererRef = ref<any>(null)
const nestedWidgetFieldName = ref<string | null>(null)
const showInstanceSelector = ref(false)
```

#### 6.2 В шаблоне, у WidgetFieldRenderer:

```vue
<WidgetFieldRenderer
    v-else-if="formFields.length > 0"
    ref="fieldRendererRef"
    :fields="formFields"
    :saving="saving"
    :showSaveButton="true"
    @save="onSaveParams"
    @select-nested-widget-instance="onSelectNestedWidgetInstance"
/>
```

#### 6.3 Добавить диалог и обработчики:

```vue
<WidgetInstanceSelectorDialog
    :visible="showInstanceSelector"
    @close="showInstanceSelector = false"
    @select="onNestedWidgetInstanceSelected"
/>
```

```ts
function onSelectNestedWidgetInstance(fieldName: string) {
    nestedWidgetFieldName.value = fieldName
    showInstanceSelector.value = true
}

function onNestedWidgetInstanceSelected(instance: any) {
    showInstanceSelector.value = false
    if (nestedWidgetFieldName.value === null) return

    fieldRendererRef.value?.setFieldValue(nestedWidgetFieldName.value, {
        id: instance.id,
        title: instance.title,
        widgetName: instance.widgetName,
        widgetId: instance.widgetId,
    })

    nestedWidgetFieldName.value = null
}
```

### Шаг 7: RemoveWidgetInstanceUseCase — очистка ссылок при удалении

**Файл:** `app/Modules/Content/Application/Actions/WidgetInstance/RemoveWidgetInstanceUseCase.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\WidgetInstance;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

final readonly class RemoveWidgetInstanceUseCase
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $instanceRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('content.widget.instance.delete')) {
            throw new \DomainException('Доступ запрещён');
        }

        // Найти все экземпляры, которые ссылаются на удаляемый
        $allInstances = $this->instanceRepository->getAll();

        foreach ($allInstances as $child) {
            $params = $child->params;
            $modifiedParams = $this->clearWidgetInstanceReferences($params, $id);
            if ($modifiedParams !== $params) {
                $child->params = $modifiedParams;
                $this->instanceRepository->save($child);
            }
        }

        // Удалить сам экземпляр
        $this->instanceRepository->delete($id);
    }

    /**
     * Рекурсивно очищает ссылки на удаляемый экземпляр в params.
     *
     * @param array $params
     * @param int $removedInstanceId
     * @return array
     */
    private function clearWidgetInstanceReferences(array $params, int $removedInstanceId): array
    {
        foreach ($params as $key => $value) {
            if ($value === $removedInstanceId) {
                $params[$key] = null;
            } elseif (is_array($value)) {
                $params[$key] = $this->clearWidgetInstanceReferences($value, $removedInstanceId);
            }
        }
        return $params;
    }
}
```

### Шаг 8: WidgetRendererService — рендер на сайте (Blade)

**Файл:** `app/Modules/Content/Application/Services/WidgetRendererService.php`

```php
<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Services;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use Illuminate\Support\Facades\View;

class WidgetRendererService
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $instanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
    ) {}

    /**
     * Рендерит экземпляр виджета по ID.
     *
     * @param int $instanceId
     * @return string HTML
     */
    public function renderInstance(int $instanceId): string
    {
        $instance = $this->instanceRepository->getById($instanceId);
        $widget = $this->widgetRepository->getById($instance->widgetId);

        $viewName = 'widgets.' . $widget->category->getValue() . '.' . $widget->slug;

        if (!View::exists($viewName)) {
            return "<!-- Widget [{$widget->name}]: шаблон {$viewName} не найден -->";
        }

        return view($viewName, [
            'params' => $instance->params,
            'widget' => $widget,
            'instance' => $instance,
        ])->render();
    }
}
```

**Файл:** `app/Providers/WidgetServiceProvider.php` (зарегистрировать сервис и Blade-директиву)

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Modules\Content\Application\Services\WidgetRendererService;

class WidgetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WidgetRendererService::class);
    }

    public function boot(): void
    {
        Blade::directive('widgetInstance', function ($expression) {
            return "<?php echo app('" . WidgetRendererService::class . "')->renderInstance($expression); ?>";
        });
    }
}
```

Использование в Blade-шаблоне виджета:

```blade
{{-- В шаблоне родительского виджета --}}
@if($params['my_widget_field'] ?? null)
    <div class="nested-widget">
        @widgetInstance($params['my_widget_field'])
    </div>
@endif
```

---

## 4. Схема данных (как это выглядит в params)

Пример `params` родительского экземпляра после сохранения:

```json
{
    "title": "Главный баннер",
    "image": { "id": 5, "src": "/uploads/banner.jpg", "alt": "Баннер" },
    "button_widget": 12,
    "items": [
        { "text": "Первый" },
        { "text": "Второй", "nested_widget": 15 }
    ]
}
```

Где:
- `button_widget: 12` — ссылка на WidgetInstance.id=12
- `items[1].nested_widget: 15` — ссылка на WidgetInstance.id=15 внутри массива

На фронте, после обогащения через `enrichWidgetInstanceValue()`, эти значения превращаются в объекты:

```json
{
    "button_widget": {
        "id": 12,
        "title": "Кнопка CTA",
        "widgetName": "Button",
        "widgetId": 3
    }
}
```

---

## 5. Что остаётся без изменений

- `WidgetEntity` — не меняется
- `WidgetInstanceEntity` — не меняется
- `WidgetSchema` — не меняется
- `ContentBlockEntity` — не меняется
- `ContentBlockController` — не меняется
- `WidgetController` — не меняется
- `WidgetFormFieldData` — не меняется (формат уже поддерживается полем `format`)
- `WidgetInstanceFormData` — не меняется
- `WidgetInstanceRepository` — не меняется (методы `getById()`, `getByWidgetId()`, `getAll()` уже есть)

---

## 6. Итоговая последовательность вызовов

1. Пользователь открывает страницу с блоками контента
2. Разворачивает блок → `ContentBlockItem` загружает форму через `GET /admin/content/widget-instances/{id}`
3. `GetWidgetInstanceFormUseCase::execute()` → `buildFields()` → встречает `format='widget_instance'`
4. Вызывает `enrichWidgetInstanceValue(15)` → загружает `WidgetInstanceEntity` через репозиторий
5. Возвращает на фронт `{ value: { id: 15, title: "Кнопка CTA", widgetName: "Button", widgetId: 3 } }`
6. `WidgetFieldRenderer` отрисовывает тег: `Виджет: Button — Кнопка CTA [x]`
7. Пользователь нажимает "Выбрать" → `ContentBlockItem` открывает `WidgetInstanceSelectorDialog`
8. В диалоге: выбор Widget → `GET /admin/content/widget-instances/by-widget/{widgetId}` → выбор экземпляра
9. Выбранный экземпляр передаётся в `WidgetFieldRenderer.setFieldValue()`
10. Пользователь нажимает "Сохранить" → `PUT /admin/content/widget-instances/{id}` с `{ params: { ..., button_widget: 12 } }`
11. На беке `UpdateWidgetInstanceUseCase` сохраняет params как есть — число 12
12. При удалении экземпляра: `RemoveWidgetInstanceUseCase` находит все ссылающиеся экземпляры и очищает ссылки
