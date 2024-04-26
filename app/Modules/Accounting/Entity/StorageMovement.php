<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Deprecated;

/**
 * Резерв товара по перемещению под Заказ
 * @property int $storage_item_id
 * @property int $movement_item_id
 * @property int $quantity
 * @property StorageItem $storageItem
 * @property MovementProduct $movementProduct
 */

#[Deprecated]
class StorageMovement extends Model
{
    public $timestamps = false;

    protected $table = 'storages_movements';

    public function storageItem()
    {
        return $this->belongsTo(StorageItem::class, 'storage_item_id', 'id');
    }

    public function movementProduct()
    {
        return $this->belongsTo(MovementProduct::class, 'movement_item_id', 'id');
    }
}
