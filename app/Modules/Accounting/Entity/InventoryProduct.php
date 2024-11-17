<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $formal - формальное наличие товара
 * @property int $cost
 * @property int $inventory_id
 *
 * @property InventoryDocument $document
 */
class InventoryProduct extends AccountingProduct
{
    public $timestamps = false;

    protected $fillable = [
        'cost',
        'formal',
    ];

    public static function new(int $product_id, int $quantity, float $cost, int $formal = null): self
    {
        $inventory = parent::baseNew($product_id, $quantity);
        $inventory->cost = $cost;
        $inventory->formal = $formal ?? $quantity;
        return $inventory;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(InventoryDocument::class, 'inventory_id', 'id');
    }
}
