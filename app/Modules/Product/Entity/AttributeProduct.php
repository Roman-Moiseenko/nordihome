<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

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


    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::retrieved(function (AttributeProduct $attr) {
            //$attr->values =  ;
        });

    }
}
