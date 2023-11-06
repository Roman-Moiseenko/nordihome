<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $product_id
 * @property int $related_id
 */
class Related extends Model
{
    protected $table = 'related_products';
    public $timestamps = false;
}
