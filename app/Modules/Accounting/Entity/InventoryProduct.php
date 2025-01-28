<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $formal - формальное наличие товара
 * @property int $cost
 * @property int $inventory_id
 *
 * @property InventoryDocument $document
 */
class InventoryProduct extends AccountingProduct
{
    public $timestamps = false;

    public $casts = [
        'cost' => 'float',
    ];

    protected $fillable = [
        'cost',
        'formal',
    ];

    public static function new(int $product_id, float $quantity, float $cost, int $formal = null): self
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

    public function toArray(): array
    {
        $array = parent::toArray();
        return array_merge($array, [
            'formal' => (float)$this->formal,
            'cost' => (float)$this->cost,
        ]);
    }


}
