<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Casts\BankPaymentCast;
use App\Modules\Base\Entity\BankPayment;
use App\Modules\Order\Entity\Payment\PaymentHelper;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 *

 * @property bool $manual - ручная оплата (в кассу, картой и по счету не ч/з банк)
 *
 * @property int $storage_id - склад оплаты по кассе/карте
 * @property BankPayment $bank_payment
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property float $amount
 * @property float $commission --
 * @property int $method
 * @property string $comment
 * @property Admin $staff
 * @property Order $order
 */
class OrderPayment extends Model
{

    const METHOD_CARD = 1;
    const METHOD_CASH = 2;
    const METHOD_ACCOUNT = 3;
    const METHOD_SBP = 4;
    const METHOD_YUKASSA = 4;

    const MANUAL_METHODS = [
        self::METHOD_CASH => 'В кассу',
        self::METHOD_CARD => 'Картой',
        self::METHOD_ACCOUNT => 'По счету (в ручную)'
    ];
    const METHODS = [
        self::METHOD_CASH => 'В кассу',
        self::METHOD_CARD => 'Картой',
        self::METHOD_ACCOUNT => 'По счету',
        self::METHOD_SBP => 'По СБП',
        self::METHOD_YUKASSA => 'ЮКасса',
    ];

    const ONLINE_METHODS = [
        'СПБ',
        'Ю касса',
        'По счету (ч/з банк)'
    ];

    protected $attributes = [
        'bank_payment' => '{}',
    ];

    protected $table = 'order_payments';
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'bank_payment' => BankPaymentCast::class,
    ];
    protected $fillable = [
        'amount',
        'method',
        'comment',
        'commission',
    ];

    public static function new(float $amount, int $method): self
    {
        return self::make([
            'amount' => $amount,
            'method' => $method,
            'commission' => 0,
            'comment' => '',
        ]);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }


    public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    //Хелперы
    public function getUserFullName(): string
    {
        return $this->order->user->fullname->getFullName();
    }

    public function htmlDate(string $field = 'created_at'): string
    {
        return $this->$field->translatedFormat('d F Y H:i');
    }

    public function methodText(): string
    {
        return self::METHODS[$this->method];
    }

    public function methodHTML(): string
    {
        return 'НЕ используется *';
    }
}
