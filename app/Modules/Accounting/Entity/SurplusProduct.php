<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $surplus_id
 *
 * @property int $cost
 * @property float $remains
 * @property SurplusDocument $document
 */
class SurplusProduct extends AccountingProduct
{
    protected $table = 'surplus_products';
    public $timestamps = false;
    protected $fillable = [
        'surplus_id',
        'cost',
    ];

    public static function new(int $product_id, float $quantity, float $cost): self
    {
        $product = self::baseNew($product_id, $quantity);
        $product->cost = $cost;
        $product->remains = $quantity;
        return $product;
    }

    public function getCost(): float
    {
        return $this->cost;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(SurplusDocument::class, 'surplus_id', 'id');
    }

    public function batchSale(mixed $batch_quantity): void
    {
        $this->remains -= $batch_quantity;
        $this->save();
    }
}
