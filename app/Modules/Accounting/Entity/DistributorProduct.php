<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $distributor_id
 * @property int $product_id
 * @property float $cost
 * @property Product $product
 * @property Distributor $distributor
 */
class DistributorProduct extends Model
{

    public $timestamps = false;
    protected $table = 'distributors_products';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

}
