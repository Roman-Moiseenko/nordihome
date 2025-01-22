<?php

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $product_id
 * @property int $size_id
 * @property bool $available
 * @property Product $product
 * @property Size $size
 */
class ProductSize extends Model
{
    public $timestamps = false;
    protected $table = 'products_sizes';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
