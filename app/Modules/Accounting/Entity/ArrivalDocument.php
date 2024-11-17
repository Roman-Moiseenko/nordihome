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
 * @property InventoryDocument $inventory
 */
class ArrivalDocument extends AccountingDocument
{
    const OPERATION_SUPPLY = 101;
    const OPERATION_REMAINS = 102;
    const OPERATION_OTHER = 110;
    const OPERATIONS = [
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
        return $amount->total ?? 0.0;
    }

    public function getQuantity(): int
    {
        $quantity = ArrivalProduct::selectRaw('SUM(quantity * 1) AS total')
            ->where('arrival_id', $this->id)
            ->first();
        return (int)($quantity->total ?? 0);
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    //*** RELATION
    public function inventory(): HasOne
    {
        return $this->hasOne(InventoryDocument::class, 'arrival_id', 'id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(MovementDocument::class, 'arrival_id', 'id');
    }

    public function expense(): HasOne
    {
        return $this->hasOne(ArrivalExpenseDocument::class, 'arrival_id', 'id');
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id');
    }

    #[Deprecated]
    public function arrivalProducts(): HasMany
    {
        return $this->hasMany(ArrivalProduct::class, 'arrival_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(ArrivalProduct::class, 'arrival_id', 'id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(RefundDocument::class, 'arrival_id', 'id');
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
        return $this->hasOne(PricingDocument::class, 'arrival_id', 'id');
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
        if (!is_null($this->inventory)) return null; //Поступление по инвентаризации

        $array = [];
        foreach ($this->refunds as $refund) {
            $array[] = $this->basedItem($refund);
        }
        foreach ($this->movements as $movement) {
            $array[] = $this->basedItem($movement);
        }
        if (!is_null($this->pricing)) $array[] = $this->basedItem($this->pricing);
        if (!is_null($this->expense)) $array[] = $this->basedItem($this->expense);

        return array_filter($array);
    }

    public function onFounded(): ?array
    {
        if (!is_null($this->inventory)) return $this->foundedGenerate($this->inventory);
        return $this->foundedGenerate($this->supply);
    }
}
