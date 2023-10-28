<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Product $product
 * @property Tag $tag
 */
class TagProduct extends Model
{
    public $timestamps = false;
    protected $table = 'tags_products';
}
