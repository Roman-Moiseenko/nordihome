<?php
declare(strict_types=1);

namespace App\Modules\Discount\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $promotion_id
 * @property int $group_id
 * @property int $discount
 */
class PromotionGroup extends Model
{
    public $timestamps = false;
    protected $table = 'promotions_groups';

}
