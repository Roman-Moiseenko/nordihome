<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $promotion_id
 * @property int $product_id
 * @property int $price
 */
class PromotionProduct extends Model
{
    public $timestamps = false;
    protected $table = 'promotions_products';
}
