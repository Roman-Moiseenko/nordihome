<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pricing_id
 * @property int $product_id
 * @property float $price_cost - себестоимость
 * @property float $price_retail
 * @property float $price_bulk
 * @property float $price_special
 * @property float $price_min
 * @property float $price_pre
 *
 * @property Product $product
 * @property PricingDocument $document
 */
class PricingProduct extends Model
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
                               float $price_cost, float $price_retail,
                               float $price_bulk, float $price_special,
                               float $price_min, float $price_pre): self
    {
        return self::make([
            'product_id' => $product_id,
            'price_cost' => $price_cost,
            'price_retail' => $price_retail,
            'price_bulk' => $price_bulk,
            'price_special' => $price_special,
            'price_min' => $price_min,
            'price_pre' => $price_pre,
        ]);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function document()
    {
        return $this->belongsTo(PricingDocument::class, 'pricing_id', 'id');
    }
}
