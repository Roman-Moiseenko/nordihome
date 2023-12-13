<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $payable_id
 * @property string $payable_type
 * @property float $amount
 * @property Carbon $paid_at
 * @property string $document
 * @property string $class //Способ оплаты - класс
 * @property array $meta //Данные о платеже
 */
class Payment extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'amount',
        'paid_at',
        'document',
        'class',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'meta' => 'json',
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public static function register(float $amount, string $class, string $document, array $meta = []): self
    {
        $payment = self::create([
            'amount' => $amount,
            'paid_at' => now(),
            'document' => $document,
            'class' => $class
        ]);

        $payment->meta = $meta;
        $payment->save();
        return $payment;
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
