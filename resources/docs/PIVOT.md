# Связь «Многие-ко-многим» на чистой архитектуре (Laravel)

## Полный принцип создания

---

## 1. Структура слоёв

Каждая связь «многие-ко-многим» реализуется как независимый модуль внутри чистой архитектуры:

```
Application/
├── DTOs/
│   ├── EntityA/
│   │   └── EntityBData.php          # DTO для EntityB в контексте EntityA (простые данные, массив)
│   └── EntityB/
│       └── EntityAData.php          # DTO для EntityA в контексте EntityB (пагинация)
├── Actions/
│   └── EntityAEntityB/              # Папка по имени таблицы (напр. RoomProduct, CategoryProduct)
│       ├── ListEntityBByEntityAUseCase.php
│       ├── ListEntityAByEntityBUseCase.php
│       ├── AssignEntityBToEntityAUseCase.php   # sync — замена всего набора
│       ├── AttachEntityBToEntityAUseCase.php   # attach — дополнение
│       ├── DetachEntityBFromEntityAUseCase.php # detach — удаление
│       ├── AssignEntityAToEntityBUseCase.php
│       ├── AttachEntityAToEntityBUseCase.php
│       └── DetachEntityAFromEntityBUseCase.php
├── Interfaces/
│   └── EntityAEntityBRepositoryInterface.php   # Контракт репозитория
Infrastructure/
├── Models/
│   └── EntityAEntityB.php            # Eloquent модель для pivot-таблицы
└── Persistence/
    └── EntityAEntityBRepository.php  # Реализация репозитория
Presentation/
└── Http/
    └── Controllers/
        └── Web/
            └── EntityAEntityBController.php  # Единый контроллер для обеих сторон
```

---

## 2. Последовательность создания (на примере Room ↔ Product)

### Шаг 1. Миграция

Таблица должна называться по алфавиту: `entity_a_entity_b` (множественное число).

```php
Schema::create('rooms_products', function (Blueprint $table) {
    $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
});
```

### Шаг 2. Модель для pivot-таблицы (Infrastructure\Models)

```php
namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class RoomProduct extends Model
{
    public $timestamps = false;
    protected $table = 'rooms_products';
}
```

### Шаг 3. DTO (Application\DTOs)

Два DTO:
- **EntityBData** (в папке EntityA) — для списка с пагинацией (толстый)
- **EntityAData** (в папке EntityB) — для списка массивом (тонкий)

```php
// Application/DTOs/Product/ProductRoomData.php
class ProductRoomData extends Data
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $code,
        public readonly string  $name,
        public readonly ?string $image,
        public readonly bool    $published,
        public readonly bool    $not_sale,
    ) {}
}

// Application/DTOs/Room/RoomProductData.php
class RoomProductData extends Data
{
    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $slug,
    ) {}
}
```

### Шаг 4. Интерфейс репозитория (Application\Interfaces)

8 методов — по 4 на каждую сторону: get, attach, sync, detach.

```php
interface RoomProductRepositoryInterface
{
    // Со стороны EntityA (Room → Products)
    public function getProductsByRoomId(int $roomId, int $perPage, int $page): LengthAwarePaginator;
    public function attachProducts(int $roomId, array $productIds): void;
    public function syncProducts(int $roomId, array $productIds): void;
    public function detachProducts(int $roomId, array $productIds): void;

    // Со стороны EntityB (Product → Rooms)
    public function getRoomsByProductId(int $productId): array;
    public function attachRooms(int $productId, array $roomIds): void;
    public function syncRooms(int $productId, array $roomIds): void;
    public function detachRooms(int $productId, array $roomIds): void;
}
```

### Шаг 5. Реализация репозитория (Infrastructure\Persistence)

```php
class RoomProductRepository implements RoomProductRepositoryInterface
{
    public function getProductsByRoomId(int $roomId, int $perPage, int $page): LengthAwarePaginator
    {
        $productIds = RoomProduct::where('room_id', $roomId)->pluck('product_id');

        return Product::orderBy('name')
            ->whereIn('id', $productIds)
            ->where(function ($query) {
                $query->doesntHave('modification')->orHas('main_modification');
            })
            ->paginate($perPage, ['*'], 'page', $page)
            ->through(fn(Product $product) => new ProductRoomData(...));
    }

    public function getRoomsByProductId(int $productId): array
    {
        $roomIds = RoomProduct::where('product_id', $productId)->pluck('room_id');

        return Room::whereIn('id', $roomIds)
            ->orderBy('name')
            ->get()
            ->map(fn(Room $room) => new RoomProductData(...))
            ->toArray();
    }

    public function attachProducts(int $roomId, array $productIds): void
    {
        $existing = RoomProduct::where('room_id', $roomId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        $new = array_diff($productIds, $existing);

        foreach ($new as $productId) {
            $pivot = new RoomProduct();
            $pivot->room_id = $roomId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    public function syncProducts(int $roomId, array $productIds): void
    {
        RoomProduct::where('room_id', $roomId)->delete();

        foreach ($productIds as $productId) {
            $pivot = new RoomProduct();
            $pivot->room_id = $roomId;
            $pivot->product_id = $productId;
            $pivot->save();
        }
    }

    public function detachProducts(int $roomId, array $productIds): void
    {
        RoomProduct::where('room_id', $roomId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    // Аналогично attachRooms, syncRooms, detachRooms
}
```

### Шаг 6. UseCase (Application\Actions\RoomProduct)

8 UseCase — простые обёртки над репозиторием с проверкой прав.

```php
readonly class ListProductByRoomUseCase
{
    public function __construct(
        private RoomProductRepositoryInterface $roomProductRepository,
    ) {}

    public function execute(int $roomId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->roomProductRepository->getProductsByRoomId($roomId, $perPage, $page);
    }
}
```

Для assign/attach/detach — добавляется проверка permission:

```php
readonly class AssignProductsToRoomUseCase
{
    public function execute(int $roomId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.room.update')) {
            throw new \DomainException('Доступ запрещён');
        }

        $this->roomProductRepository->syncProducts($roomId, $productIds);
    }
}
```

### Шаг 7. Контроллер (Presentation\Http\Controllers\Web)

Один контроллер на обе стороны связи. Все методы принимают `int $id`.

```php
readonly class RoomProductController
{
    public function __construct(
        // Со стороны Room
        private ListProductByRoomUseCase      $listProductByRoomUseCase,
        private AssignProductsToRoomUseCase   $assignProductsToRoomUseCase,
        private AttachProductToRoomUseCase    $attachProductToRoomUseCase,
        private DetachProductFromRoomUseCase  $detachProductFromRoomUseCase,
        // Со стороны Product
        private ListRoomByProductUseCase      $listRoomByProductUseCase,
        private AssignRoomsToProductUseCase   $assignRoomsToProductUseCase,
        private AttachRoomsToProductUseCase   $attachRoomsToProductUseCase,
        private DetachRoomsFromProductUseCase $detachRoomsFromProductUseCase,
    ) {}

    // === Действия от комнаты ===
    public function roomProducts(int $id, Request $request): JsonResponse { ... }
    public function assignRoomProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse { ... }
    public function attachRoomProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse { ... }
    public function detachRoomProducts(int $id, Request $request, UserPermission $userPermission): JsonResponse { ... }

    // === Действия от товара ===
    public function productRooms(int $id): JsonResponse { ... }
    public function assignProductRooms(int $id, Request $request, UserPermission $userPermission): JsonResponse { ... }
    public function attachProductRooms(int $id, Request $request, UserPermission $userPermission): JsonResponse { ... }
    public function detachProductRooms(int $id, Request $request, UserPermission $userPermission): JsonResponse { ... }
}
```

### Шаг 8. Маршруты (routes/web.php)

Маршруты для связи добавляются **в соответствующие группы** EntityA и EntityB.

```php
use App\Modules\Catalog\Presentation\Http\Controllers\Web\RoomProductController;

// В группе room — методы со стороны комнаты
Route::group(['prefix' => 'room', 'as' => 'room.'], function () {
    Route::get('/{id}/products', [RoomProductController::class, 'roomProducts'])->name('products');
    Route::post('/{id}/products/sync', [RoomProductController::class, 'assignRoomProducts'])->name('products.sync');
    Route::post('/{id}/products/attach', [RoomProductController::class, 'attachRoomProducts'])->name('products.attach');
    Route::delete('/{id}/products/detach', [RoomProductController::class, 'detachRoomProducts'])->name('products.detach');
});

// В группе product — методы со стороны товара
Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
    Route::get('/{id}/rooms', [RoomProductController::class, 'productRooms'])->name('rooms');
    Route::post('/{id}/rooms/sync', [RoomProductController::class, 'assignProductRooms'])->name('rooms.sync');
    Route::post('/{id}/rooms/attach', [RoomProductController::class, 'attachProductRooms'])->name('rooms.attach');
    Route::delete('/{id}/rooms/detach', [RoomProductController::class, 'detachProductRooms'])->name('rooms.detach');
});
```

> **Важно про имена маршрутов**: при добавлении нескольких `products` в одной группе (например, старый `get('/products/{id}')` и новый `get('/{id}/products')`) необходимо переименовывать их через `->name('...')`, чтобы избежать конфликтов имён.

### Шаг 9. ServiceProvider

Зарегистрировать биндинг в `register()`.

```php
$this->app->bind(
    RoomProductRepositoryInterface::class,
    RoomProductRepository::class
);
```

### Шаг 10. Если нужен объединённый список (основная связь + pivot)

Если у EntityA есть прямое поле (например, `main_category_id`), а также связь через pivot, и нужно отдавать объединённый список:

1. Добавить метод `findAllByEntityAId` в **основной** репозиторий EntityB
2. В SQL/Query: `WHERE main_entity_a_id = ? OR id IN (pivot_ids)`, без дубликатов
3. Создать отдельный UseCase `ListAllEntityBByEntityAUseCase`
4. Заменить в контроллере старый UseCase на новый

```php
public function findAllByCategoryId(int $categoryId, int $perPage, int $page): LengthAwarePaginator
{
    $pivotProductIds = CategoryProduct::where('category_id', $categoryId)->pluck('product_id');

    return Product::orderBy('name')
        ->where(function ($query) use ($categoryId, $pivotProductIds) {
            $query->where('main_category_id', $categoryId);
            if (!empty($pivotProductIds)) {
                $query->orWhereIn('id', $pivotProductIds);
            }
        })
        ->where(fn($q) => $q->doesntHave('modification')->orHas('main_modification'))
        ->paginate(...);
}
```

---

## 3. Сводная таблица файлов

| № | Файл | Назначение |
|---|------|-----------|
| 1 | `Database/Migrations/..._create_table.php` | Создание pivot-таблицы |
| 2 | `Infrastructure/Models/EntityAEntityB.php` | Eloquent модель для pivot |
| 3 | `Application/DTOs/EntityA/EntityBData.php` | DTO со стороны A (пагинация) |
| 4 | `Application/DTOs/EntityB/EntityAData.php` | DTO со стороны B (массив) |
| 5 | `Application/Interfaces/EntityAEntityBRepositoryInterface.php` | Контракт репозитория |
| 6 | `Infrastructure/Persistence/EntityAEntityBRepository.php` | Реализация репозитория |
| 7-14 | `Application/Actions/EntityAEntityB/*UseCase.php` | 8 UseCase |
| 15 | `Presentation/Http/Controllers/Web/EntityAEntityBController.php` | Контроллер |
| 16 | `routes/web.php` (дополнение) | Маршруты |
| 17 | `Providers/...ServiceProvider.php` (дополнение) | Биндинг |

---

## 4. Правила именования

| Элемент | Правило | Пример |
|---------|---------|--------|
| Папка Actions | `EntityAEntityB` (единственное, по алфавиту) | `RoomProduct`, `CategoryProduct` |
| Имя класса UseCase | `{Action}{Source}{Dest}UseCase` | `ListProductByRoomUseCase` |
| Имя контроллера | `{EntityA}{EntityB}Controller` | `RoomProductController` |
| Имя репозитория (interface) | `{EntityA}{EntityB}RepositoryInterface` | `RoomProductRepositoryInterface` |
| Имя модели (Infrastructure) | `{EntityA}{EntityB}` | `RoomProduct` |
| Таблица в БД | `{entity_a}_{entity_b}` (мн.ч., по алфавиту) | `rooms_products`, `categories_products` |

**Порядок в составных именах**: A — та сущность, со стороны которой чаще работают (например, Room → Product, Category → Product).
