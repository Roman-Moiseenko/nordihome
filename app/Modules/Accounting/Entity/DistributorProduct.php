<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $distributor_id
 * @property int $product_id
 * @property float $cost
 */
class DistributorProduct extends Model
{

    public $timestamps = false;
    protected $table = 'distributors_products';

}
