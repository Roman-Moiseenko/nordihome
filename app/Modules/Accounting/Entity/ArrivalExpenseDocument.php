<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $arrival_id
 * @property boolean $currency
 * @property ArrivalExpenseItem[] $items
 * @property ArrivalDocument $arrival
 */
class ArrivalExpenseDocument extends AccountingDocument
{

    public $fillable = [
        'currency',
    ];

    protected string $blank = 'Дополнительные расходы';

    public static function register(int $staff_id): ArrivalExpenseDocument
    {
        $expense = parent::baseNew($staff_id);
        $expense->currency = false;
        //$expense->save();
        return $expense;
    }

    public function getAmount(): float
    {
        $amount = 0;
        foreach ($this->items as $item) {
            $amount += $item->getAmount();
        }
        if ($this->currency) return (int)ceil($this->arrival->exchange_fix * $amount);
        return $amount;
    }

    public function products(): HasMany
    {
        throw new \DomainException('ArrivalExpenseDocument not Product');
    }

    public function arrival(): BelongsTo
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ArrivalExpenseItem::class, 'expense_id', 'id');
    }

    function documentUrl(): string
    {
        return route('admin.accounting.arrival.expense.show', ['expense' => $this->id], false);
    }

    public function onBased(): ?array
    {
        return null;
    }

    public function onFounded(): ?array
    {
        return $this->foundedGenerate($this->arrival);
    }
}
