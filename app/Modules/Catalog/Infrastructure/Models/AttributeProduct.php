<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $attribute_id
 * @property int $product_id
 * @property string $value
 */
class AttributeProduct extends Model
{
   // public mixed $values;

    public $timestamps = false;
    protected $table = 'attributes_products';

}
