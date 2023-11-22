<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property float $value
 * @property string $founded
 */
class ProductPricing extends Model
{
    protected $table = 'product_pricing';
    protected $fillable = [
        'value',
        'founded'
    ];

    public function product()
    {
        //return $this->
    }
}
