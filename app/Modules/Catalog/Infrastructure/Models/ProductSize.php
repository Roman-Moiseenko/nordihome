<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property array $values
 * @property bool $is_active
 */
class ProductSize extends Model
{
    public $timestamps = false;

    protected $table = 'products_sizes';

    protected $fillable = [
        'product_id',
        'values',
        'is_active',
    ];

    protected $casts = [
        'values' => 'array',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
