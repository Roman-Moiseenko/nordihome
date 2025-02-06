<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Casts\BankPaymentCast;
use App\Modules\Base\Entity\BankPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $order_id
 * @property int $staff_id
 * @property bool $completed
 * @property bool $manual - ручная оплата (в кассу, картой и по счету не ч/з банк)
 *
 * @property int $storage_id - склад оплаты по кассе/карте
 * @property BankPayment $bank_payment
 * @property int $shopper_id - Получатель и
 * @property int $trader_id - ... Продавец, если не найден Заказ. Обнуляются, при назначении Заказа!
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property float $amount
 * @property float $commission --
 * @property int $method
 * @property string $comment
 * @property bool $is_refund - Возврат
 * @property Admin $staff
 * @property Order $order
 * @property Organization $shopper
 * @property Organization $trader
 * @property OrderExpenseRefund $refund
 */
class OrderPayment extends Model
{
    const int METHOD_CARD = 1;
    const int METHOD_CASH = 2;
    const int METHOD_ACCOUNT = 3;
    const int METHOD_SBP = 4;
    const int METHOD_YUKASSA = 5;

    const array MANUAL_METHODS = [
        self::METHOD_CASH => 'В кассу',
        self::METHOD_CARD => 'Картой',
        self::METHOD_ACCOUNT => 'По счету (в ручную)'
    ];
    const array METHODS = [
        self::METHOD_CASH => 'В кассу',
        self::METHOD_CARD => 'Картой',
        self::METHOD_ACCOUNT => 'По счету',
        self::METHOD_SBP => 'По СБП',
        self::METHOD_YUKASSA => 'ЮКасса',
    ];

    const array ONLINE_METHODS = [
        'СПБ',
        'Ю касса',
        'По счету (ч/з банк)'
    ];

    protected $attributes = [
        'bank_payment' => '{}',
        'is_refund' => false,
    ];

    protected $table = 'order_payments';
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'bank_payment' => BankPaymentCast::class,
        'is_refund' => 'bool'
    ];
    protected $fillable = [
        'amount',
        'method',
        'comment',
        'commission',
        'is_refund',
    ];

    public static function new(float $amount, int $method): self
    {
        return self::make([
            'amount' => $amount,
            'method' => $method,
            'commission' => 0,
            'comment' => '',
            'is_refund' => false,
        ]);
    }

    public function isCash(): bool
    {
        return $this->method == OrderPayment::METHOD_CASH;
    }

    public function isCard(): bool
    {
        return $this->method == OrderPayment::METHOD_CARD;
    }

    public function isAccount(): bool
    {
        return $this->method == OrderPayment::METHOD_ACCOUNT;
    }

    public function isRefund(): bool
    {
        return $this->is_refund == true;
    }
    /**
     * Документ проведен
     */
    final public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    /**
     * Провести документ
     */
    final public function completed(): void
    {
        $this->completed = true;
        $this->save();
    }

    /**
     * Вернуть в работу
     */
    final public function work(): void
    {
        $this->completed = false;
        $this->save();

    }

    public function refund(): HasOne
    {
        return $this->hasOne(OrderExpenseRefund::class, 'order_payment_id', 'id');
    }

    public function shopper(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'shopper_id', 'id');
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'trader_id', 'id');
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

    final public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    final public function scopeWork($query)
    {
        return $query->where('completed', false);
    }

}
