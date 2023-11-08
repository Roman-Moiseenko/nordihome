<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $group_id
 * @property int $product_id
 */
class GroupProduct extends Model
{
    public $timestamps = false;
    public $table ='groups_products';
}
