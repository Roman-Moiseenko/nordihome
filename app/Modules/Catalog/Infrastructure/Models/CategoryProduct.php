<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $category_id
 * @property int $product_id
 * @property Category $category
 */
class CategoryProduct extends Model
{
    public $timestamps = false;
    protected $touches = ['category'];
    protected $table = 'categories_products';

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
