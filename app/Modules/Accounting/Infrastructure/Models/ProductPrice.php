<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Models;

use App\Modules\Catalog\Infrastructure\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property string $type
 * @property float $amount
 * @property string $currency
 * @property Carbon $set_at
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
        'set_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
