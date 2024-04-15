<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $staff_id
 * @property int $storage_id
 * @property int $product_id
 * @property int $quantity
 * @property int $order_item_id - возможно перенести связь через OrderItem, тогда в стек можно заносить разные заявки
 * @property int $supply_id - null по-умолчанию
 * @property string $comment
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property OrderItem $orderItem
 * @property SupplyDocument $supply - поставка по текущему запросу
 * @property Product $product
 * @property \App\Modules\Admin\Entity\Admin $staff
 * @property Storage $storage
 */
class SupplyStack extends Model
{
    protected $table = 'supply_stack';

    public $timestamps = false;
    protected $fillable = [
        'staff_id',
        'supply_id',
        'storage_id',
        'created_at',
        'product_id',
        'quantity',
        'comment',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $product_id, int $quantity, int $staff_id, int $storage_id, string $comment): self
    {
        return self::create([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'staff_id' => $staff_id,
            'storage_id' => $storage_id,
            'created_at' => now(),
            'supply_id' => null,
            'comment' => $comment,
        ]);
    }

    public function supply()
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function status(): string
    {
        if (is_null($this->supply)) return 'В обработке';
        return 'Заказ поставщику: ' . $this->supply->statusHTML();
    }

    public function orderItem()
    {
        return $this->hasOne(OrderItem::class, 'supply_stack_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }


}
