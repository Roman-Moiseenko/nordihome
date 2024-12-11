<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property float $quantity
 * @property float $cost
 * @property string $name
 * @property int $expense_id
 * @property ArrivalExpenseDocument $expense
 */
class ArrivalExpenseItem extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'quantity',
        'cost',
        'name'
    ];

    public static function new(string $name, float $quantity, float $cost)
    {
        return self::make([
            'quantity' => $quantity,
            'cost' => $cost,
            'name' => $name
        ]);
    }

    public function getAmount(): float
    {
        return $this->quantity * $this->cost;
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(ArrivalExpenseDocument::class, 'expense_id', 'id');
    }
}
