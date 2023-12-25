<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Request;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $update_at
 * @property RequestItem[] $items
 * @property
 */
class RequestToOrder extends Model
{

    protected $fillable =[
        'order_id',
    ];

    //TODO Заявка на заказ к Order
    public static function register(int $order_id): self
    {
        return self::create([
            'order_id' => $order_id,
        ]);
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class, 'request_id', 'id');
    }
}
