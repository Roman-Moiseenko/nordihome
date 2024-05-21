<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Admin\Entity\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 * @property Carbon $created_at
 * @property int $action --- ???? string
 * @property string $value
 *
 * @property Order $order
 */
class OrderLog extends Model
{
    //const
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'staff_id',
        'action',
        'value',
        'created_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function register(int $order_id, int $staff_id, $action, string $value)
    {
        self::create([
            'order_id' => $order_id,
            'staff_id' => $staff_id,
            'action' => $action,
            'value' => $value,
            'created_at' => now(),
                ]
        );
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

}
