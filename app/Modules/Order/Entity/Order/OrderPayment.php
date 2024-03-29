<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Order\Entity\Payment\PaymentHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property Carbon $created_at
 * @property float $amount
 * @property string $method
 * @property string $document
 *
 * @property Order $order
 */

//TODO Проверить
class OrderPayment extends Model
{
    public $timestamps = false;
    protected $table = 'order_payments';
    protected $casts = [
        'created_at' => 'datetime',
    ];
    protected $fillable = [
        'amount',
        'created_at',
        'method',
        'document'
    ];

    public static function new(float $amount, string $method, string $document): self
    {
        return self::make([
            'amount' => $amount,
            'created_at' => now(),
            'method' => $method,
            'document' => $document
        ]);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders', 'id');
    }

    public function methodHTML(): string
    {
        return PaymentHelper::nameType($this->method);
    }
}
