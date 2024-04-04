<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Таблица с времеными данными для фиксации статуса отправки и быстрого доступа к данным
 * @property int $id
 * @property int $storage_id
 * @property int $product_id
 * @property int $quantity
 * @property int $movement_product_id
 * @property Carbon $created_at
 *
 * @property Storage $storage
 * @property Product $product
 * @property MovementProduct $movement_product
 */
class StorageDepartureItem extends Model
{
    public $timestamps = false;
    protected $table = 'storage_departure_items';
    protected $fillable = [
        'storage_id',
        'product_id',
        'quantity',
        'movement_product_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function new(int $product_id, int $quantity, int $movement_product_id): self
    {
        return self::make([
            'product_id' => $product_id,
            'quantity' => $quantity,
            'movement_product_id' => $movement_product_id,
            'created_at' => now(),
        ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function movement_product()
    {
        return $this->belongsTo(MovementProduct::class, 'movement_product_id', 'id');
    }
}
