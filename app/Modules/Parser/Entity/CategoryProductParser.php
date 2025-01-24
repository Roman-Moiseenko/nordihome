<?php

namespace App\Modules\Parser\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $product_id
 * @property int $category_id
 */
class CategoryProductParser extends Model
{
    protected $table = 'parser_categories_products';
    public $timestamps = false;

}
