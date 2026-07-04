<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property string $type
 * @property float $amount
 * @property string $currency
 * @property string $set_at
 * @property string|null $founded
 * @property string|null $comment
 *
 * @property-read Product $product
 */
class ProductPrice extends Model
{
    protected $table = 'product_prices';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'type',
        'amount',
        'currency',
        'set_at',
        'founded',
        'comment',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'amount'     => 'float',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
