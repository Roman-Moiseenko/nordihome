<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $surplus_id
 *
 * @property int $cost
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

    public static function new(int $product_id, int $quantity, float $cost): self
    {
        $product = self::baseNew($product_id, $quantity);
        $product->cost = $cost;
        return $product;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(SurplusDocument::class, 'surplus_id', 'id');
    }
}
