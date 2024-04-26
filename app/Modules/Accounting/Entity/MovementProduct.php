<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $movement_id
 * @property int $product_id
 * @property int $quantity
 * @property int $order_item_id
 * @property Product $product
 * @property MovementDocument $document
 * @property StorageDepartureItem $departureItem
 * @property StorageArrivalItem $arrivalItem
 * @property OrderItem $orderItem
 */
class MovementProduct extends Model implements MovementItemInterface
{
    protected $table = 'movement_products';
    public $timestamps = false;
    protected $fillable = [
        'movement_id',
        'product_id',
        'order_item_id',
        'quantity',
        'cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(MovementDocument::class, 'movement_id', 'id');
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function departureItem()
    {
        return $this->hasOne(StorageDepartureItem::class, 'movement_product_id', 'id');
    }

    public function arrivalItem()
    {
        return $this->hasOne(StorageArrivalItem::class, 'movement_product_id', 'id');
    }


    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
}
