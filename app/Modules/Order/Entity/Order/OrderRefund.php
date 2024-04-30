<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $comment
 * @property int $status
 * @property OrderRefundItem[] $items
 * @property OrderRefundAddition[] $additions
 */
class OrderRefund extends Model
{
    const NEW = 80;
    const CONFIRMED = 81; //Проставляет ответственный за деньги, руководитель
    const PAID = 82; //Проставляет Бухгалтер
    const COMPLETED = 89; //Проставляет менеджер, отчитался за работу!

    protected $table = 'order_refunds';
    protected $fillable = [
        'order_id',
        'staff_id',
        'created_at',
        'updated_at',
        'comment',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $order_id, int $staff_id, string $comment): self
    {
        return self::create([
            'order_id' => $order_id,
            'staff_id' => $staff_id,
            'comment' => $comment,
            'status' => self::NEW,
        ]);
    }

    public function items()
    {
        return $this->hasMany(OrderRefundItem::class, 'refund_id', 'id');
    }
    public function additions()
    {
        return $this->hasMany(OrderRefundAddition::class, 'refund_id', 'id');
    }
}
