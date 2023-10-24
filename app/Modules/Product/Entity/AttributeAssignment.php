<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $attribute_id
 * @property int $product_id
 * @property string $value
 */
class AttributeAssignment extends Model
{

    public $timestamps = false;
    protected $table = 'attributes_products';
}
