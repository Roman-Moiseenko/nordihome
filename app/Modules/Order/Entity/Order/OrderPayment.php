<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property float $amount
 * @property string $method
 * @property string $document
 * @property Admin $staff
 * @property Order $order
 */

//TODO Проверить
class OrderPayment extends Model
{
    protected $table = 'order_payments';
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $fillable = [
        'amount',
        'method',
        'document',
        'staff_id',
    ];

    public static function new(float $amount, string $method, string $document): self
    {
        return self::make([
            'amount' => $amount,
            'method' => $method,
            'document' => $document
        ]);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function methodHTML(): string
    {
        return PaymentHelper::nameType($this->method);
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    //Хелперы
    public function getUserFullName(): string
    {
        return $this->order->user->fullname->getFullName();
    }
}
