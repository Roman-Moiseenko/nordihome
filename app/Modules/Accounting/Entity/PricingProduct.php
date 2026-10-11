<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

/**
 * @property int $id
 * @property int $pricing_id
 * @property float $price_cost - себестоимость
 * @property float $price_retail
 * @property float $price_bulk
 * @property float $price_special
 * @property float $price_min
 * @property float $price_pre
 *
 * @property PricingDocument $document
 */
class PricingProduct extends AccountingProduct
{
    public $timestamps = false;
    public $casts = [
        'product_id' => 'float',
        'price_cost' => 'float',
        'price_retail' => 'float',
        'price_bulk' => 'float',
        'price_special' => 'float',
        'price_min' => 'float',
        'price_pre' => 'float',
    ];
    protected $table = 'pricing_products';
    protected $fillable = [
        'product_id',
        'price_cost',
        'price_retail',
        'price_bulk',
        'price_special',
        'price_min',
        'price_pre',
    ];

    public static function new(int $product_id,
                               float $price_cost, float $price_retail,
                               float $price_bulk, float $price_special,
                               float $price_min, float $price_pre): self
    {
        $item = self::make([
            'product_id' => $product_id,
        ]);

        $item->price_cost = $price_cost;
        $item->price_retail = $price_retail;
        $item->price_bulk = $price_bulk;
        $item->price_special = $price_special;
        $item->price_min = $price_min;
        $item->price_pre = $price_pre;
        return $item;
    }


    public function document(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PricingDocument::class, 'pricing_id', 'id');
    }

    public function setQuantity(float $quantity): void
    {
        throw new \DomainException('Установка цен не содержит поле quantity');
    }

    public function getQuantity(): float
    {
        throw new \DomainException('Установка цен не содержит поле quantity');
    }
}
