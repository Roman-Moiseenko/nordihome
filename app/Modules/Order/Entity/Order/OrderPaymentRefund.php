<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Deprecated;
use function now;

/**
 * @property int $id
 * @property int $payment_id
 * @property float $amount
 * @property string $comment
 * @property Carbon $created_at
 * @property OrderPayment $payment
 */

//TODO Удалить с таблицей
#[Deprecated]
class OrderPaymentRefund extends Model
{

    protected $table = 'order_payment_refunds';
    protected $fillable = [
        'payment_id',
        'amount',
        'comment',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public static function register(int $payment_id, float $amount, string $comment): self
    {
        return self::create([
            'payment_id' => $payment_id,
            'amount' => $amount,
            'comment' => $comment,
            'created_at' => now(),
        ]);
    }

    public function payment()
    {
        return $this->belongsTo(OrderPayment::class, 'payment_id', 'id');
    }
}
