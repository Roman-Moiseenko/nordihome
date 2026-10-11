<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity\Order;


use App\Modules\Auth\Infrastructure\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $expense_id
 * @property int $worker_id
 * @property int $work
 * @property OrderExpense $expense
 * @property Staff $worker
 */
class OrderExpenseWorker extends Model
{
    public $timestamps = false;
    protected $table = 'order_expenses_workers';

    public function expense(): BelongsTo
    {
        return $this->belongsTo(OrderExpense::class, 'expense_id', 'id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'worker_id', 'id');
    }
}
