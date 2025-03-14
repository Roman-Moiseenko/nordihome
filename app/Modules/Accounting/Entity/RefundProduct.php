<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $cost_currency
 * @property int $refund_id
 * @property RefundDocument $document
 */
class RefundProduct extends AccountingProduct
{
    public $timestamps = false;
    protected $fillable = [
        'cost_currency',
    ];

    public static function new(int $product_id, float $quantity, float $cost_currency): self
    {
        $product = parent::baseNew($product_id, $quantity);
        $product->cost_currency = $cost_currency;

        return $product;
    }

    public function setCost(float $cost_currency): void
    {
        $this->cost_currency = $cost_currency;
    }

    public function getCost(): float
    {
        return $this->cost_currency;
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(RefundDocument::class, 'refund_id', 'id');
    }

    /**
     * @return SupplyProduct
     */
    public function getSupplyProduct(): AccountingProduct
    {
        return $this->document->supply->getProduct($this->product_id);
    }

    /**
     * @return ArrivalProduct
     */
    public function getArrivalProduct(): AccountingProduct
    {
        return $this->document->arrival->getProduct($this->product_id);
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        return array_merge($array, [
            'cost_currency' => (float)$this->cost_currency,
        ]);
    }
}
