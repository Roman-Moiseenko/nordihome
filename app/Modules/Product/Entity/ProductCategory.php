<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $product_id
 * @property int $category_id
 */
class ProductCategory extends Model
{

    public $timestamps = false;
    protected $table = 'products_categories';

}
