<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use App\Modules\Catalog\Infrastructure\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string|null $ad_id
 * @property string $ad_number
 * @property int|null $product_id
 * @property int|null $avito_category_id
 * @property string $name
 * @property string|null $description
 * @property int $base_price
 * @property int|null $sell_price
 * @property int $price_package
 * @property string|null $tn_ved
 * @property string|null $okpd2
 * @property string|null $country
 * @property bool $delivery
 * @property bool $active
 * @property bool $sale
 * @property bool $in_stock
 */
class AvitoProduct extends Model
{
    protected $fillable = [
        'ad_id',
        'ad_number',
        'product_id',
        'avito_category_id',
        'name',
        'description',
        'base_price',
        'sell_price',
        'price_package',
        'tn_ved',
        'okpd2',
        'country',
        'delivery',
        'in_stock',
        'active',
        'sale',
    ];

    protected $casts = [
        'delivery' => 'boolean',
        'in_stock' => 'boolean',
        'active' => 'boolean',
        'sale' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(AvitoCategory::class, 'avito_category_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
