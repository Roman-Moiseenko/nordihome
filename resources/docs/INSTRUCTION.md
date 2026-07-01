# Инструкция по созданию модулей (Clean Architecture)

## Структура модуля

```
app/Modules/{ModuleName}/
├── Domain/
│   ├── Entities/          # Domain Entity (бизнес-логика)
│   └── ValueObjects/      # Value Objects модуля (Slug, Image, Meta — в Shared)
├── Application/
│   ├── Interfaces/        # RepositoryInterface
│   ├── DTOs/              # Spatie Laravel Data
│   └── Actions/           # UseCase
├── Infrastructure/
│   ├── Models/            # Eloquent Model (именование: {Entity}Model)
│   ├── Persistence/       # Реализация RepositoryInterface
│   └── Casts/             # Кастомные касты (если нужны)
├── Presentation/
│   └── Http/
│       └── Controllers/   # Контроллеры (только "тонкие")
├── Providers/
│   └── {Module}ServiceProvider.php
├── Database/
│   └── Migrations/
└── routes/
    └── web.php
```

---

## 1. Domain Entity (сущность)

**Назначение:** Бизнес-сущность, содержащая ТОЛЬКО бизнес-логику. Без Eloquent, без запросов к БД.

**Правила:**
- Используем PHP 8.4 property hooks (`get`/`set`)
- Все поля с модификатором `public` (для доступа из UseCase/репозитория)
- **Общие ValueObjects** (Slug, Email, Meta, Image) — в `app/Modules/Shared/Domain/ValueObjects/`
- **VO, специфичные для модуля** — в `Domain/ValueObjects/` самого модуля
- Конструктор принимает ТОЛЬКО обязательные для создания поля
- Методы состояния (`publish()`, `unpublish()`, `isPublished()`) — тут
- `children`, связанные сущности — массив, заполняется репозиторием
- Именование - к названию сщности добавляем `Entity`, например сущность Товар - `ProductEntity`

**Пример:**

```php
final class RoomEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    public Slug $slug {
        get => $this->slug;
        set => $this->slug = $value;
    }

    public bool $published = false {
        get => $this->published;
        set => $this->published = $value;
    }

    public ?int $parentId = null {
        get => $this->parentId;
        set => $this->parentId = $value;
    }

    /** @var RoomEntity[] */
    public array $children = [] {
        get => $this->children;
    }

    public function __construct(
        string $name,
        Slug $slug,
        ?int $parentId = null
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->parentId = $parentId;
    }

    public function publish(): void { $this->published = true; }

    public function unpublish(): void { $this->published = false; }

    public function isPublished(): bool { return $this->published; }
}
```

---

## 2. Value Objects

**Назначение:** Неизменяемые объекты-значения.

**Где лежат:**
- **Общие для всех модулей** — `app/Modules/Shared/Domain/ValueObjects/`
  - `Slug`, `Email`, `Image`, `Meta`, `Address`, `PhoneNumber`
- **Специфичные для модуля** — `app/Modules/{ModuleName}/Domain/ValueObjects/`

**Пример (общий — из Shared):**

```php
final class Image
{
    public function __construct(
        private readonly string  $url,
        private readonly ?string $alt = null,
    ) {}

    public function getUrl(): string { return $this->url; }
    public function getAlt(): ?string { return $this->alt; }
}
```

**Пример (общий — из Shared):**

```php
final class Slug
{
    public function __construct(private readonly string $value)
    {
        if (empty($value)) throw new \InvalidArgumentException('Slug не может быть пустым');
    }

    public function __toString(): string { return $this->value; }
}
```

---

## 3. RepositoryInterface

**Назначение:** Контракт для работы с хранилищем.

**Где лежит:** `app/Modules/{ModuleName}/Application/Interfaces/`

**Правила:**
- Методы возвращают Domain Entity или `void`
- В сигнатуре нет Eloquent, нет Request
- `getAll()` — возвращает все
- `getById(int $id)` — возвращает одну сущность (выбрасывает исключение если не найдено)
- `save(Entity $entity)` — сохраняет (create/update), возвращает сохранённую сущность
- `delete(int $id)` — удаляет
- Специфические методы пишем по необходимости

**Пример:**

```php
interface RoomRepositoryInterface
{
    /** @return RoomEntity[] */
    public function getAll(): array;

    public function getById(int $id): RoomEntity;

    public function save(RoomEntity $room): RoomEntity;

    public function delete(int $id): void;

    /** @return RoomEntity[] */
    public function getTree(): array;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;
}
```

---

## 4. DTO (Spatie Laravel Data)

**Назначение:** Объекты передачи данных с валидацией.

**Где лежит:** `app/Modules/{ModuleName}/Application/DTOs/{ModelName}/`

**Наследование:** Все DTO наследуются от `Spatie\LaravelData\Data`

**Правила:**
- Поля только `public readonly`
- Атрибуты валидации через `#[Required]`, `#[StringType]`, `#[Max(255)]`, `#[Nullable]` и т.д.
- `validateAndCreate()` — встроенный статический метод от `Data`, валидирует данные и создаёт DTO
- `from(array $data)` — без валидации (если нужна ручная)
- Для маппинга Entity → DTO пишем статический метод `fromEntity()`
- Валидация используется только для тех DTO которые вносят изменения, для передачи данных на фронтенд, валидация не используется
- Для всех DTO которые передают данные на фронтенд обязательно поле `id` Модели

**Наименование DTO:**
- Начинается с название Модели {ModuleName}
- Далее идет действие или action, например для показа данный используем View, для index(), также Index, для списков List или Tree, создание/обновление - Create/Update
- Заканичваются Data
- Например, иерархический список категорий CategoryTreeData


**Пример DTO для создания:**

```php
class RoomCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
        #[Nullable, StringType, Max(255)]
        public readonly ?string $slug,
        #[Nullable, Numeric]
        public readonly ?int $parentId,
    ) {}
}
```

Использование в контроллере:
```php
$dto = RoomCreateData::validateAndCreate($request->all());
```

**Пример DTO для списка/вывода:**

```php
class RoomViewData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $image,
        public readonly ?int $parentId,
        /** @var RoomViewData[] */
        public readonly array $children = [],
    ) {}

    public static function fromEntity(RoomEntity $room): self
    {
        return new self(
            id: $room->id,
            name: $room->name,
            slug: (string) $room->slug,
            image: $room->image?->getUrl(),
            parentId: $room->parentId,
            children: array_map(
                fn(RoomEntity $child) => self::fromEntity($child),
                $room->children
            ),
        );
    }
}
```

**Основные атрибуты валидации (Spatie Laravel Data):**

| Атрибут | Описание |
|---------|----------|
| `#[Required]` | Обязательное поле |
| `#[Nullable]` | Может быть null |
| `#[StringType]` | Строка |
| `#[Numeric]` | Число |
| `#[Max(255)]` | Максимальная длина |
| `#[BooleanType]` | Boolean |
| `#[Email]` | Email |
| `#[Url]` | URL |
| `#[In(['a', 'b'])]` | Только из списка |

---

## 5. UseCase

**Назначение:** Один сценарий использования (один публичный метод `execute()`).

**Где лежит:** `app/Modules/{ModuleName}/Application/Actions/{ModelName}/`

**Правила:**
- Класс `readonly`
- В конструкторе — только зависимости (RepositoryInterface)
- Метод `execute()`:
  - Принимает DTO (если нужно) и `UserPermission`
  - Возвращает Entity или void
- Проверка прав доступа — **первым делом** внутри `execute()`
- Права доступа определяются так `{ModuleName}.{ModelName}.{Action}`, пример ниже
- Никакой логики работы с БД — только вызов репозитория и бизнес-логика Entity

**Пример:**

```php
readonly class CreateRoomUseCase
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
    ) {}

    public function execute(RoomCreateData $dto, UserPermission $userPermission): RoomEntity
    {
        if (!$userPermission->can('catalog.room.create')) {
            throw new \DomainException('Доступ запрещён');
        }

        $slug = new Slug($dto->slug ?: $dto->name);

        if ($this->roomRepository->existsSlug((string)$slug)) {
            $slug = new Slug((string)$slug . '-' . uniqid());
        }

        $room = new RoomEntity(
            name: $dto->name,
            slug: $slug,
            parentId: $dto->parentId ?: null,
        );

        return $this->roomRepository->save($room);
    }
}
```

**Список стандартных UseCase:**

| UseCase | Принимает | DTO | Описание |
|---------|-----------|-----|----------|
| `Index{Entity}UseCase` | `UserPermission` | — | Список всех сущностей |
| `Create{Entity}UseCase` | DTO + `UserPermission` | `{Entity}CreateData` | Создание |
| `View{Entity}UseCase` | `int $id` + `UserPermission` | — | Просмотр одной |
| `Update{Entity}UseCase` | `int $id` + DTO + `UserPermission` | `{Entity}UpdateData` | Обновление |
| `Remove{Entity}UseCase` | `int $id` + `UserPermission` | — | Удаление |

---

## 6. Eloquent Model

**Назначение:** Модель БД (Active Record). **Только** для ORM, без бизнес-логики.

**Где лежит:** `app/Modules/{ModuleName}/Infrastructure/Models/`

**Правила:**
- Именование: `{Entity}` (например, `Room`)
- `$timestamps = false` если в таблице нет `created_at`/`updated_at`
- Используем трейты: `NodeTrait` для NestedSet, `ImageField`, `IconField` для фото
- `$casts` — касты для json-полей
- `$fillable` — поля для массового заполнения (для seeders/factory)

**Пример:**

```php
class RoomModel extends Model
{
    use NodeTrait, ImageField, IconField;

    protected $table = 'rooms';

    public $timestamps = false;

    protected $fillable = [
        'name', 'slug', 'svg', 'meta', 'published', 'parent_id',
    ];

    protected $casts = [
        'meta' => 'array',
        'published' => 'boolean',
    ];
}
```

**Трейты изображений:**
- `ImageField` — полиморфная связь `image()` (одно главное изображение)
- `IconField` — полиморфная связь `icon()` (одна иконка)
- Методы: `$model->image->getUploadUrl()`, `$model->image->alt`, `$model->image->file`

---

## 7. Repository (реализация)

**Назначение:** Реализация RepositoryInterface. Преобразует Eloquent Model → Domain Entity и обратно.

**Где лежит:** `app/Modules/{ModuleName}/Infrastructure/Persistence/`

**Правила:**
- Приватный метод `hydrate(Model $model): Entity` — преобразует Eloquent модель в Domain Entity
- Сохранение: через поля модели напрямую (НЕ через массивы):
  ```php
  // Правильно:
  $model->name = $room->name;
  $model->save();

  // НЕПРАВИЛЬНО:
  $model->update([...]);
  RoomModel::create([...]);
  ```
- После сохранения — `$model->fresh()->load(['image', 'icon'])` для получения актуальных данных
- Для списка с отношениями — `with(['image', 'icon'])`

**Пример сохранения:**

```php
public function save(RoomEntity $room): RoomEntity
{
    $model = $room->id
        ? RoomModel::findOrFail($room->id)
        : new RoomModel();

    $model->name = $room->name;
    $model->slug = (string) $room->slug;
    $model->svg = $room->svgIcon;
    $model->published = $room->isPublished();
    $model->meta = $room->meta ? [
        'title' => $room->meta->getTitle(),
        'description' => $room->meta->getDescription(),
    ] : [];

    if ($room->parentId !== null) {
        $model->parent_id = $room->parentId;
    }

    $model->save();

    return $this->hydrate($model->fresh()->load(['image', 'icon']));
}
```

**Пример гидратации:**

```php
private function hydrate(RoomModel $model): RoomEntity
{
    $entity = new RoomEntity(
        name: $model->name,
        slug: new Slug($model->slug),
        parentId: $model->parent_id,
    );

    $entity->id = $model->id;

    if ($model->relationLoaded('image') && $model->image && $model->image->file) {
        $entity->image = new Image(
            url: $model->image->getUploadUrl(),
            alt: $model->image->alt,
        );
    }

    $entity->published = (bool) $model->published;

    $metaData = is_array($model->meta) ? $model->meta : [];
    $entity->meta = new Meta(
        title: $metaData['title'] ?? '',
        description: $metaData['description'] ?? '',
    );

    $entity->left = $model->_lft ?? 0;
    $entity->right = $model->_rgt ?? 0;
    $entity->depth = $model->depth ?? 0;

    return $entity;
}
```

---

## 8. ServiceProvider (биндинги)

**Где лежит:** `app/Modules/{ModuleName}/Providers/{Module}ServiceProvider.php`

В методе `register()` добавляем биндинг интерфейса к реализации:

```php
public function register()
{
    $this->app->bind(
        RoomRepositoryInterface::class,
        RoomRepository::class
    );
}
```

---

## 9. Controller

**Назначение:** Тонкий контроллер. Только принимает запрос, вызывает UseCase, возвращает ответ.

**Где лежит:** `app/Modules/{ModuleName}/Presentation/Http/Controllers/Web/`

**Правила:**
- В конструкторе — DI UseCase (автоматически через Laravel DI)
- Методы:
  - `index()` — список
  - `store(Request)` — создание (получает DTO через `validateAndCreate`)
  - `show(int $id)` — просмотр одной
  - `update(int $id, Request)` — обновление
  - `destroy(int $id)` — удаление
- Для Inertia — передаём массив через `fromEntity()` (или коллекцию DTO)

**Пример:**

```php
class RoomController
{
    public function __construct(
        public readonly IndexRoomUseCase $indexRoomUseCase,
        public readonly CreateRoomUseCase $createRoomUseCase,
        public readonly ViewRoomUseCase $viewRoomUseCase,
        public readonly UpdateRoomUseCase $updateRoomUseCase,
        public readonly RemoveRoomUseCase $removeRoomUseCase,
    ) {}

    public function store(Request $request, UserPermission $userPermission)
    {
        $dto = RoomCreateData::validateAndCreate($request->all());

        $room = $this->createRoomUseCase->execute($dto, $userPermission);
        return redirect()->route('admin.catalog.room.show', $room->id);
    }
}
```

---

## 10. Маршруты (Routes)

**Где лежит:** `app/Modules/{ModuleName}/routes/web.php`

**Пример:**

```php
use App\Modules\Catalog\Presentation\Http\Controllers\Web\RoomController;

Route::group([
    'middleware' => 'role:admin|staff',
    'prefix' => 'admin.catalog',
    'as' => 'admin.catalog.',
], function () {
    Route::resource('room', RoomController::class)->except(['create', 'edit']);
});
```

---

## Общий порядок создания нового модуля

1. **Domain Entity** — бизнес-сущность с property hooks
2. **ValueObjects** (если нужны новые, специфичные для модуля) — в `Domain/ValueObjects/` модуля
3. **RepositoryInterface** — контракт на работу с БД
4. **DTO** — Spatie Data с валидацией + `fromEntity()`
5. **UseCase** — сценарии (проверка прав + бизнес-логика)
6. **Eloquent Model** — в `Infrastructure/Models`
7. **Repository** — реализация с `hydrate()`
8. **ServiceProvider** — биндинг
9. **Controller** — тонкий, вызывает UseCase
10. **Routes** — resource + дополнительные маршруты
11. **Breadcrumbs** + **Menu**
