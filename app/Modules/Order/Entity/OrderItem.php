<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property int $base_cost
 * @property int $discount_cost
 * @property string $discount_name //TODO ОПЦИИ
 */
class OrderItem extends Model
{

}
