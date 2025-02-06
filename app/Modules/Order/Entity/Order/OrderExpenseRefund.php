<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;

use App\Modules\Admin\Entity\Admin;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $expense_id
 * @property int $staff_id
 * @property string $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $comment
 * @property int $reason
 * @property bool $completed
 * @property int $order_payment_id
 * @property OrderPayment $payment
 * @property OrderExpenseRefundItem[] $items
 * @property OrderExpenseRefundAddition[] $additions
 *
 * @property OrderExpense $expense
 * @property Admin $staff
 */
class OrderExpenseRefund extends Model
{
    use HtmlInfoData;

    const int REASON_DEFECT = 1701;
    const int REASON_TIME_LIMIT = 1702;
    const int REASON_REFUSAL = 1703;
    const int REASON_INACCURATE = 1704;

    const array REASONS = [
        null => 'Не определена',
        self::REASON_DEFECT => 'Брак товара',
        self::REASON_TIME_LIMIT => 'Возврат по времени ожидания',
        self::REASON_REFUSAL => 'Отказ покупателя',
        self::REASON_INACCURATE => 'Недостоверные данные',
    ];

    protected $table = 'order_expense_refunds';
    protected $fillable = [
        'expense_id',
        'staff_id',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $expense_id, int $staff_id, int $reason): self
    {
        return self::create([
            'expense_id' => $expense_id,
            'staff_id' => $staff_id,
            'reason' => $reason,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->completed == true;
    }


    public function isPayment(): bool
    {
        if (!is_null($this->order_payment_id) && $this->payment->isCompleted()) return true;
        return false;
    }

    /**
     * Провести документ
     */
    public function completed(): void
    {
        $this->completed = true;
        $this->save();
    }

    /**
     * Вернуть в работу
     */
    public function work(): void
    {
        $this->completed = false;
        $this->save();

    }

    public function amount(): float
    {
        return $this->amountItems() + $this->amountAdditions();
    }

    public function amountItems(): float
    {
        $amount = 0.0;
        foreach ($this->items as  $item) {
            $amount += $item->quantity * $item->expenseItem->orderItem->sell_cost;
        }
        return $amount;
    }

    public function amountAdditions(): float
    {
        $amount = 0.0;
        foreach ($this->additions as  $addition) {
            $amount += $addition->amount;
        }
        return $amount;
    }

/*
    public function getBalanceOrder(): float
    {
        return $this->order->getPaymentAmount() - $this->order->getExpenseAmount();
    }

    public function getRefundAmount(): float
    {
        return min($this->getBalanceOrder(), $this->getAmount());
    }

    public function getAmount(): float
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += $item->quantity * $item->orderItem->sell_cost;
        }

        foreach ($this->additions as $addition) {
            $amount += $addition->amount;
        }
        return $amount;
    }
*/
    public function  getQuantity(): float
    {
        $quantity = 0;
        foreach ($this->items as $item) {
            $quantity += $item->quantity;
        }
        return $quantity;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderExpenseRefundItem::class, 'refund_id', 'id');
    }

    public function additions(): HasMany
    {
        return $this->hasMany(OrderExpenseRefundAddition::class, 'refund_id', 'id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(OrderPayment::class, 'order_payment_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderRefundPayment::class, 'refund_id', 'id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function relatedDocuments(): array
    {
        $documents = [];
        if (!is_null($this->order_payment_id)) {
            $documents[] = [
                'name' => 'Платеж на возврат от ' . $this->payment->created_at->format('d-m-y'),
                'link' => route('admin.order.payment.show', $this->payment, false),
                'type' => 'primary',
                'completed' => $this->payment->isCompleted(),
            ];
        }
        return $documents;
    }


}
