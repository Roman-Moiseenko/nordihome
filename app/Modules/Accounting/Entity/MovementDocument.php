<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int $id
 * @property int $storage_out
 * @property int $storage_in
 * @property int $status
 * @property string $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $completed
 * @property string $comment Комментарий к документу, пока отключена, на будущее
 *
 * @property Storage $storageOut
 * @property Storage $storageIn
 * @property MovementProduct[] $movementProducts
 */
class MovementDocument extends Model implements MovementInterface
{
    const STATUS_DRAFT = 11; //Черновик
    const STATUS_DEPARTURE = 12; //На убытие
    const STATUS_ARRIVAL = 13; //В Пути
    const STATUS_COMPLETED = 14; //Исполнен
    const STATUSES = [
        self::STATUS_DRAFT => 'Черновик',
        self::STATUS_DEPARTURE => 'На отбытии',
        self::STATUS_ARRIVAL => 'В пути',
        self::STATUS_COMPLETED => 'Завершен',
    ];

    protected $table = 'movement_documents';

    protected $fillable = [
        'storage_out',
        'storage_in',
        'number',
        'basic',
        'status',
        'comment',
        'expense_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $number, int $storage_out, int $storage_in, string $comment): self
    {
        return self::create([
            'number' => $number,
            'storage_out' => $storage_out,
            'storage_in' => $storage_in,
            'status' => self::STATUS_DRAFT,
            'comment' => $comment,
        ]);
    }


    public function setExpense(int $expense_id)
    {
        $this->expense_id = $expense_id;
        $this->save();
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $this->movementProducts()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'cost' => $product->getLastPrice()
        ]);
    }

    public function departure()
    {
        $this->status = self::STATUS_DEPARTURE;
        $this->save();
    }

    public function arrival()
    {
        $this->status = self::STATUS_ARRIVAL;
        $this->save();
    }

    public function completed()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function order():? Order
    {
        return $this->belongsToMany(Order::class, 'orders_movements', 'movement_id', 'order_id')->first();
    }

    public function movementProducts()
    {
        return $this->hasMany(MovementProduct::class, 'movement_id', 'id');
    }

    public function storageOut()
    {
        return $this->belongsTo(Storage::class, 'storage_out', 'id');
    }

    public function storageIn()
    {
        return $this->belongsTo(Storage::class, 'storage_in', 'id');
    }

    public function expense()
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

    public function isCompleted(): bool
    {
        return $this->status == self::STATUS_COMPLETED;
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->movementProducts as $item) {
            if ($item->product_id == $product_id) return true;
        }
        return false;
    }

    #[ArrayShape([
        'quantity' => 'int',
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

    public function statusHTML(): string
    {

        if ($this->status == 0) return self::STATUSES[self::STATUS_COMPLETED];
        return self::STATUSES[$this->status];
    }
}
