<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

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
                               float $price_cost = null, float $price_retail = null,
                               float $price_bulk = null, float $price_special = null,
                               float $price_min = null, float $price_pre = null): self
    {
        $item = self::make([
            'product_id' => $product_id,
        ]);
        $item->price_cost = $price_cost ?? $item->product->getPriceCost();
        $item->price_retail = $price_retail ?? $item->product->getPriceRetail();
        $item->price_bulk = $price_bulk ?? $item->product->getPriceBulk();
        $item->price_special = $price_special ?? $item->product->getPriceSpecial();
        $item->price_min = $price_min ?? $item->product->getPriceMin();
        $item->price_pre = $price_pre ?? $item->product->getPricePre();

        return $item;
    }


    public function document(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PricingDocument::class, 'pricing_id', 'id');
    }

    public function setQuantity(int $quantity): void
    {
        throw new \DomainException('Установка цен не содержит поле quantity');
    }

    public function getQuantity(): int
    {
        throw new \DomainException('Установка цен не содержит поле quantity');
    }
}
