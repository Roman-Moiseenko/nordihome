<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $min
 * @property int $max
 * @property int $product_id
 * @property bool $buy
 *
 * @property Product $product
 */
class BalanceProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'min',
        'buy',
        //'product_id',
        'max',
    ];

    protected $casts = [
        'buy' => 'boolean',
    ];

    public static function new(int $min, int $max = null, $buy = true): self
    {
        return self::make([
            'min' => $min,
            'buy' => $buy,
            'max' => $max,
        ]);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
