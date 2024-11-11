<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Product\Entity\Product;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int $id
 * @property int $storage_out
 * @property int $storage_in
 * @property int $status
 * @property int $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $completed
 * @property string $comment Комментарий к документу, пока отключена, на будущее
 * @property int $staff_id - автор документа
 *
 * @property Storage $storageOut
 * @property Storage $storageIn
 * @property MovementProduct[] $movementProducts
 * @property Admin $staff
 */
class MovementDocument extends Model implements AccountingDocument
{
    use HtmlInfoData;

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
        'staff_id',
        'arrival_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $storage_out, int $storage_in, int $staff_id, int $arrival_id): self
    {
        return self::create([
            'storage_out' => $storage_out,
            'storage_in' => $storage_in,
            'status' => self::STATUS_DRAFT,
            'staff_id' => $staff_id,
            'arrival_id' => $arrival_id,
        ]);
    }


    public function setExpense(int $expense_id)
    {
        $this->expense_id = $expense_id;
        $this->save();
    }

    public function addProduct(Product $product, int $quantity, int $order_item_id = null): void
    {
        $this->movementProducts()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'order_item_id' => $order_item_id,
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
    //** HELPERS */

    public function htmlNum(): string
    {
        if (!is_null($this->order())) return 'Заказ ' . $this->order()->htmlNum();
        if (empty($this->number)) return 'б/н';
        return '№ ' . str_pad((string)$this->number, 6, '0', STR_PAD_LEFT);
    }

    public function setNumber()
    {
        $this->number = MovementDocument::where('number', '<>', null)->count() + 1;
        $this->save();
    }

    public function statusHTML(): string
    {
        if ($this->status == 0) return self::STATUSES[self::STATUS_COMPLETED];
        return self::STATUSES[$this->status];
    }


    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
        $this->save();
    }

    public function getComment(): string
    {
        return $this->comment;
    }

}
