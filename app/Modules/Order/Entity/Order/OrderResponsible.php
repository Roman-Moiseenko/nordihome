<?php


namespace App\Modules\Order\Entity\Order;


use App\Entity\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Ответственные сотрудники по заказу
 * Class OrderResponsible
 * @package App\Modules\Order\Entity\Order
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 * @property Carbon $created_at
 * @property Carbon $close_at
 * @property int $staff_post
 * @property Admin $staff
 *
 */
class OrderResponsible extends Model
{
    const POST_MANAGER = 1;
    const POST_LOGGER = 2;
    const POST_CASHER = 3;

    public $timestamps = false;
    protected $fillable = [
        'staff_id',
        'staff_post',
        'created_at',
        'close_at'
    ];

    protected $table = 'order_responsible';
    protected $casts = [
        'created_at' => 'datetime',
        'close_at' => 'datetime',
        ];

    public static function registerManager(int $staff_id): self
    {
        return self::make([
            'staff_id' => $staff_id,
            'staff_post' => self::POST_MANAGER,
            'created_at' => now(),
        ]);
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }
}
