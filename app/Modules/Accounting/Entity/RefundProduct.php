<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $cost_currency
 * @property int $refund_id
 * @property RefundDocument $refund
 */
class RefundProduct extends AccountingProduct
{
    public $timestamps = false;
    protected $fillable = [
        'cost_currency',
    ];

    public static function new(int $product_id, int $quantity, float $cost_currency): self
    {
        $product = parent::baseNew($product_id, $quantity);
        $product->cost_currency = $cost_currency;

        return $product;
    }

    public function setCost(float $cost_currency): void
    {
        $this->cost_currency = $cost_currency;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(RefundDocument::class, 'refund_id', 'id');
    }
}
