<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Traits\CompletedFieldModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $number
 * @property float $amount
 * @property int $distributor_id
 * @property int $supply_id
 * @property int $trader_id --Плательщик
 * @property string $comment
 * @property string $account -- счет плательщика
 * @property int $staff_id
 * @property bool $completed
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Trader $trader
 * @property SupplyDocument $supply
 * @property Distributor $distributor
 * @property Admin $staff
 */
class PaymentDocument extends Model
{

    use CompletedFieldModel;

    protected $attributes = [
        'comment' => '',
    ];
    protected $fillable = [
        'number',
        'amount',
        'distributor_id',
        'completed',
        'staff_id',
    ];

    public static function register(int $distributor_id, float $amount, int $staff_id): self
    {
        return self::create([
            'number' => self::count() + 1,
            'amount' => $amount,
            'distributor_id' => $distributor_id,
            'completed' => false,
            'staff_id' => $staff_id,
        ]);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function trader(): BelongsTo
    {
        return $this->belongsTo(Trader::class);
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(SupplyDocument::class);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }
}
