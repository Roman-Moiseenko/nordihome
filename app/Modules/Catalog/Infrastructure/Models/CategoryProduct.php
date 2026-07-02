<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $category_id
 * @property int $product_id
 */
class CategoryProduct extends Model
{
    public $timestamps = false;
    protected $table = 'categories_products';
}
