<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Payment;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property float $amount
 * @property Carbon $created_at
 * @property Carbon $paid_at
 * @property string $document
 * @property string $class //Способ оплаты - класс
 * @property array $meta //Данные о платеже
 * @property int $purpose
 * @property string $comment
 */
class PaymentOrder extends Model
{
    /**
     * Назначения платежа $purpose
     */
    const PAY_ORDER = 101;
    const PAY_DELIVERY = 102;
    const PAY_PACKING = 103;
    const PAY_ASSEMBLY = 104;
    const PAY_OTHER = 109;

    const PAYS = [
        self::PAY_ORDER => 'Платеж за заказ',
        self::PAY_DELIVERY => 'Платеж за доставку',
        self::PAY_PACKING => 'Платеж за упаковку',
        self::PAY_ASSEMBLY => 'Платеж за сборку',
        self::PAY_OTHER => 'Другие платежи',
    ];

    protected $table = 'payment_orders';

    public $timestamps = false;
    protected $fillable = [
        'amount',
        'paid_at',
        'document',
        'class',
        'purpose',
        'created_at',
        'comment',
        'meta'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'meta' => 'json',
    ];


    public static function new(float $amount, string $class, string $document, int $purpose, array $meta = []): self
    {
        $payment = self::make([
            'amount' => $amount,
            'document' => $document,
            'class' => $class,
            'purpose' => $purpose,
            'created_at' => now(),
        ]);
        $payment->meta = $meta;
        return $payment;
    }

    /**
     * Установка или смена назначения платежа
     * @param int $purpose
     * @param string $comment
     * @return void
     */
    public function setType(int $purpose, string $comment = ''): void
    {
        $this->purpose = $purpose;
        $this->comment = $comment;
        $this->save();
    }

    public function isPaid(): bool
    {
        return !is_null($this->paid_at);
    }

    public function purposeHTML(): string
    {
        return self::PAYS[$this->purpose];
    }



    /////////////////////////////////////////////////////////////////////////////
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
