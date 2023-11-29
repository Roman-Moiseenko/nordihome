<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 */
class Cart extends Model
{
    protected $table = 'cart';
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
}
