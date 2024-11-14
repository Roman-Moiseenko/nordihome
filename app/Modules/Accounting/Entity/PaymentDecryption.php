<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Order\Entity\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property float $amount
 * @property string $order_bank
 * @property int $supply_id
 * @property int $payment_id
 * @property int $order_id
 *
 * @property SupplyDocument $supply
 * @property Order $order
 * @property PaymentDocument $payment
 */
class PaymentDecryption extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'amount',
        'supply_id',
    ];

    public static function register(float $amount, int $supply_id = null, string $order_bank = '')
    {
        return self::make([
            'amount' => $amount,
            'supply_id' => $supply_id,
            'order_bank' => $order_bank,
        ]);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(PaymentDocument::class, 'payment_id');
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'supply_id');
    }

}
