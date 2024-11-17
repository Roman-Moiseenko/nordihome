<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $arrival_id
 * @property int $storage_id
 * @property int $distributor_id
 *
 * @property ArrivalDocument $arrival
 * @property Storage $storage
 * @property RefundProduct[] $products
 * @property Distributor $distributor
 */
class RefundDocument extends AccountingDocument
{
    protected string $blank = 'Возврат поставщику';
    public static function register(int $staff_id, int $arrival_id, int $distributor_id, int $storage_id): self
    {
        $refund = parent::baseNew($staff_id);

        $refund->arrival_id = $arrival_id;
        $refund->distributor_id = $distributor_id;
        $refund->storage_id = $storage_id;
        $refund->save();
        return $refund;
    }
    //*** GET-...

    public function getAmount(): float
    {
        $amount = RefundProduct::selectRaw('SUM(quantity * cost_currency) AS total')
            ->where('refund_id', $this->id)
            ->first();
        return $amount->total ?? 0.0;

    }

    public function getQuantity(): int
    {
        $quantity = RefundProduct::selectRaw('SUM(quantity * 1) AS total')
            ->where('refund_id', $this->id)
            ->first();
        return (int)($quantity->total ?? 0);
    }

    public function getStorage(): ?Storage
    {
        return $this->storage;
    }

    public function storageName(): string
    {
        if (!is_null($storage = $this->getStorage())) return $storage->name;
        return '';
    }

    public function products(): HasMany
    {
        return $this->hasMany(RefundProduct::class, 'refund_id', 'id');
    }

    public function arrival(): BelongsTo
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }


    function documentUrl(): string
    {
        return route('admin.accounting.refund.show', ['refund' => $this->id], true);
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
