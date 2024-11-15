<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $movement_id
 * @property int $order_item_id
 * @property MovementDocument $document
 * @property StorageDepartureItem $departureItem
 * @property StorageArrivalItem $arrivalItem
 * @property OrderItem $orderItem
 */
class MovementProduct extends AccountingProduct
{
    protected $table = 'movement_products';
    public $timestamps = false;
    protected $fillable = [
        'movement_id',
        'product_id',
        'order_item_id',
        'quantity',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(MovementDocument::class, 'movement_id', 'id');
    }


    public function departureItem(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StorageDepartureItem::class, 'movement_product_id', 'id');
    }

    public function arrivalItem(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StorageArrivalItem::class, 'movement_product_id', 'id');
    }


    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }
}
