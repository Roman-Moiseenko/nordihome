<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderMovement;
use App\Modules\Product\Entity\Product;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $storage_out
 * @property int $storage_in
 * @property int $status
 * @property int $arrival_id
 *
 * @property Storage $storageOut
 * @property Storage $storageIn
 * @property ArrivalDocument $arrival
 * @property Order $order
 * @property MovementProduct[] $movementProducts
 */
class MovementDocument extends AccountingDocument
{
    use HtmlInfoData;

    const STATUS_DRAFT = 11; //Черновик
    const STATUS_DEPARTURE = 12; //На убытие
    const STATUS_ARRIVAL = 13; //В Пути
    const STATUS_FINISHED = 14; //Исполнен
    const STATUSES = [
        self::STATUS_DRAFT => 'Черновик',
        self::STATUS_DEPARTURE => 'На отбытии',
        self::STATUS_ARRIVAL => 'В пути',
        self::STATUS_FINISHED => 'Завершен',
    ];

    protected string $blank = 'Перемещение запасов';
    protected $table = 'movement_documents';

    protected $fillable = [
        'storage_out',
        'storage_in',
        'basic',
        'status',
        'expense_id',
        'arrival_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $storage_out, int $storage_in, int $staff_id, ?int $arrival_id): self
    {
        $movement = parent::baseNew($staff_id);
        $movement->storage_out = $storage_out;
        $movement->storage_in = $storage_in;
        $movement->status = self::STATUS_DRAFT;
        $movement->arrival_id = $arrival_id;
        $movement->save();
        return $movement;
    }


    public function setExpense(int $expense_id): void
    {
        $this->expense_id = $expense_id;
        $this->save();
    }

    public function addProduct(int $product_id, float $quantity, int $order_item_id = null): void
    {
        $this->products()->create([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'order_item_id' => $order_item_id,
        ]);
    }

    public function statusDeparture(): void
    {
        $this->status = self::STATUS_DEPARTURE;
        $this->save();
    }

    public function statusArrival(): void
    {
        $this->status = self::STATUS_ARRIVAL;
        $this->save();
    }

    public function statusCompleted(): void
    {
        $this->status = self::STATUS_FINISHED;
        $this->save();
    }

    public function arrival(): BelongsTo
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function order()
    {
        return $this->hasOneThrough(
            Order::class,
            OrderMovement::class,
            'movement_id', 'id',
            'id', 'order_id');

        /*return $this->hasOneThrough(Organization::class, ShopperOrganization::class,
            'user_id', 'id',
            'id', 'organization_id')
            ->where('shopper_organizations.default', true);
        */
        //return $this->belongsToMany(Order::class, 'orders_movements', 'movement_id', 'order_id')->first();
    }

    #[Deprecated]
    public function movementProducts(): HasMany
    {
        return $this->hasMany(MovementProduct::class, 'movement_id', 'id');
    }

    public function storageOut(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_out', 'id');
    }

    public function storageIn(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_in', 'id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }

    public function isDraft(): bool
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function isDeparture(): bool
    {
        return $this->status == self::STATUS_DEPARTURE;
    }

    public function isArrival(): bool
    {
        return $this->status == self::STATUS_ARRIVAL;
    }

    public function isFinished(): bool
    {
        return $this->status == self::STATUS_FINISHED;
    }

    public function getQuantity(): float
    {
        $quantity = MovementProduct::selectRaw('SUM(quantity * 1) AS total')
            ->where('movement_id', $this->id)
            ->first();
        return (float)$quantity->total ?? 0;
    }

    #[ArrayShape([
        'quantity' => 'float',
        'cost' => 'float',
    ])]
    public function getInfoData(): array
    {
        $quantity = 0;
        $cost = 0;
        foreach ($this->movementProducts as $item) {
            $quantity += $item->quantity;
            $cost += $item->quantity * ($item->cost ?? 0);
        }
        return [
            'quantity' => $quantity,
            'cost' => $cost,
        ];
    }

    //** HELPERS */
    public function statusHTML(): string
    {
        if ($this->status == 0) return self::STATUSES[self::STATUS_FINISHED];
        return self::STATUSES[$this->status];
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    public function products(): HasMany
    {
        return $this->hasMany(MovementProduct::class, 'movement_id', 'id');

    }

    function documentUrl(): string
    {
        return route('admin.accounting.movement.show', ['movement' => $this->id], false);
    }

    public function onBased(): ?array
    {
        return null;
    }

    public function onFounded(): ?array
    {
        return $this->foundedGenerate($this->arrival);
    }
}
