<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property float $amount
 * @property Carbon $paid_at
 * @property string $document
 * @property string $class //Способ оплаты - класс
 */
class Payment extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'amount',
        'paid_at',
        'document',
        'class',
        'finished'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public static function register(int $order_id, float $amount, string $class, string $document): self
    {
        return self::create([
            'order_id' => $order_id,
            'amount' => $amount,
            'paid_at' => now(),
            'document' => $document,
            'class' => $class
        ]);
    }


    public static function namespace(): string
    {
        return __NAMESPACE__;
    }


    public function nameType(): string
    {
        $class = __NAMESPACE__ . "\\" . $this->class;
        return $class::name();
    }
}
