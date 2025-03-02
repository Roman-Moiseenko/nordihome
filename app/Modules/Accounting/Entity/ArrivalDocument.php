<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $distributor_id
 * @property int $storage_id
 * @property int $currency_id
 * @property int $supply_id
 * @property float $exchange_fix //Курс на момент создания документа
 * @property int $operation
 * @property string $gtd
 * @property Storage $storage
 * @property Currency $currency
 * @property ArrivalProduct[] $arrivalProducts //Заменить на products
 * @property ArrivalProduct[] $products
 * @property Distributor $distributor
 * @property SupplyDocument $supply
 * @property PricingDocument $pricing
 * @property RefundDocument[] $refunds
 * @property MovementDocument[] $movements
 * @property ArrivalExpenseDocument $expense
 * @property ArrivalExpenseDocument[] $expenses
 */
class ArrivalDocument extends AccountingDocument
{
    const int OPERATION_SUPPLY = 101;
    const int OPERATION_REMAINS = 102;
    const int OPERATION_OTHER = 110;
    const array OPERATIONS = [
        self::OPERATION_SUPPLY => 'Поступление от поставщика',
        self::OPERATION_REMAINS => 'Поступление остатков',
        self::OPERATION_OTHER => 'Другое',
    ];

    protected string $blank = 'Приходная накладная';
    protected $table = 'arrival_documents';
    protected $fillable = [
        'distributor_id',
        'storage_id',
        'currency_id',
        'exchange_fix',
        'supply_id',
    ];


    public static function register(?int $distributor_id, int $storage_id, Currency $currency, int $staff_id = null): self
    {
        $arrival = parent::baseNew($staff_id);
        $arrival->distributor_id = $distributor_id;
        $arrival->storage_id = $storage_id;
        $arrival->currency_id = $currency->id;
        $arrival->exchange_fix = $currency->getExchange();
        $arrival->operation = self::OPERATION_SUPPLY;
        $arrival->save();
        return $arrival;
    }

    //*** IS-...
    public function isSupply(): bool
    {
        return !is_null($this->supply_id);
    }

    //*** SET-...

    //*** GET-...
    public function getAmount(): float
    {
        $amount = ArrivalProduct::selectRaw('SUM(quantity * cost_currency) AS total')
            ->where('arrival_id', $this->id)
            ->first();
        return (float)$amount->total ?? 0.0;
    }

    public function getAmountVAT(): float
    {
        $amount = 0;
        foreach ($this->products as $product) {
            if (!is_null($product->product->VAT) && !is_null($product->product->VAT->value))
                $amount += $product->quantity * $product->cost_currency * ($product->product->VAT->value / 100);
        }
        return ceil($amount * 100) / 100;
    }

    public function getQuantity(): float
    {
        $quantity = ArrivalProduct::selectRaw('SUM(quantity * 1) AS total')
            ->where('arrival_id', $this->id)
            ->first();
        return (float)($quantity->total ?? 0);
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    //*** RELATION
    public function movements(): HasMany
    {
        return $this->hasMany(MovementDocument::class, 'arrival_id', 'id')->withTrashed();
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ArrivalExpenseDocument::class, 'arrival_id', 'id')->withTrashed();
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id')->withTrashed();
    }

    public function products(): HasMany
    {
        return $this->hasMany(ArrivalProduct::class, 'arrival_id', 'id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(RefundDocument::class, 'arrival_id', 'id')->withTrashed();
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(PricingDocument::class, 'arrival_id', 'id')->withTrashed();
    }

    public function operationText(): string
    {
        return self::OPERATIONS[$this->operation];
    }

    function documentUrl(): string
    {
        return route('admin.accounting.arrival.show', ['arrival' => $this->id], false);
    }

    public function onBased(): ?array
    {
        $array = [];
        foreach ($this->refunds as $refund) {
            $array[] = $this->basedItem($refund);
        }
        foreach ($this->movements as $movement) {
            $array[] = $this->basedItem($movement);
        }
        if (!is_null($this->pricing)) $array[] = $this->basedItem($this->pricing);
        foreach ($this->expenses as $expense) {
            $array[] = $this->basedItem($expense);
        }

        //if (!is_null($this->expense)) $array[] = $this->basedItem($this->expense);
        $array = array_filter($array);
        return empty($array) ? null : $array;
    }

    public function onFounded(): ?array
    {
        return $this->foundedGenerate($this->supply);
    }

    public function getExpenseAmount(): float|int|null
    {
        if ($this->expenses()->count() == 0) return null;
        $amount = 0;
        foreach ($this->expenses as $expense) {
            $amount += $expense->getAmount();
        }
        return $amount;
    }

    public function delete(): void
    {
        foreach ($this->expenses as $expense) {
            $expense->delete();
        }
        foreach ($this->refunds as $refund) {
            $refund->delete();
        }
        foreach ($this->movements as $movement) {
            $movement->delete();
        }
        if (!is_null($this->pricing)) $this->pricing->delete();

        parent::delete();
    }

    public function restore(): void
    {
        parent::restore();
        foreach ($this->expenses as $arrival) {
            $arrival->restore();
        }
        foreach ($this->refunds as $refund) {
            $refund->restore();
        }
        foreach ($this->movements as $movement) {
            $movement->restore();
        }
        if (!is_null($this->pricing)) $this->pricing->restore();

    }

    public function forceDelete(): void
    {
        foreach ($this->expenses as $arrival) {
            $arrival->forceDelete();
        }
        foreach ($this->refunds as $refund) {
            $refund->forceDelete();
        }
        foreach ($this->movements as $movement) {
            $movement->forceDelete();
        }
        if (!is_null($this->pricing)) $this->pricing->forceDelete();

        parent::forceDelete();
    }
}
