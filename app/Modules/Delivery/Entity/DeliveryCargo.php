<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Entity;

use App\Modules\Guide\Entity\CargoCompany;
use App\Modules\Order\Entity\Order\OrderExpense;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $expense_id
 * @property int $cargo_company_id
 * @property string $track_number
 * @property int $completed
 * @property int $status
 * @property Carbon $completed_at
 * @property OrderExpense $expense
 * @property CargoCompany $cargo
 */
class DeliveryCargo extends Model
{
    const int STATUS_NEW = 1301;
    const int STATUS_SHIPPED = 1302;

    /**
     * Добавить
     */
    const int STATUS_ISSUED = 1330;
    const int STATUS_REFUND = 1331;

    const array STATUSES = [
        self::STATUS_NEW => 'Получено',
        self::STATUS_SHIPPED => 'Отгружено',
        /**
         *
         */
        self::STATUS_ISSUED => 'Выдано',
        self::STATUS_REFUND => 'Возврат',
    ];

    protected $table = 'deliveries';
    protected $fillable = [
        'expense_id',
        'cargo_company_id',
        'track_number',
        'completed'
    ];

    public static function new(int $cargo_company_id, string $track_number)
    {
        return self::make([
            //'expense_id' => $expense_id,
            'cargo_company_id' => $cargo_company_id,
            'track_number' => $track_number,
            'completed' => false,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    public function completed(Carbon $date = null): void
    {
        $this->completed_at = $date ?? now();
        $this->completed = true;
        $this->save();
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(CargoCompany::class, 'cargo_company_id', 'id');
    }
}
