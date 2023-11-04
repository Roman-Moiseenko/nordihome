<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $equivalent_id
 * @property int $product_id
 */
class EquivalentProduct extends Model
{
    public $timestamps = false;
    protected $table = 'equivalents_products';
}
