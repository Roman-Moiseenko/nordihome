<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $cart_id
 * @property int $product_id
 * @property int $quantity
 * @property int $reserve_id //Связанная позиция в резерве
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $options_json //Опции товара [id1, id2, ...]
 *
 * @property Product $product
 * @property Reserve $reserve
 */
class CartItem extends Model
{

    protected $table = 'cart_products';
}
