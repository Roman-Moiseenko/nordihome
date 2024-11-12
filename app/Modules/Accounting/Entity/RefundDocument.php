<?php

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $supply_id
 * @property int $arrival_id
 * @property int $storage_id
 * @property int $distributor_id
 *
 * @property SupplyDocument $supply
 * @property ArrivalDocument $arrival
 * @property Storage $storage
 * @property RefundProduct[] $products
 * @property Distributor $distributor
 */
class RefundDocument extends AccountingDocument
{
    public static function register(int $staff_id, int $distributor_id): self
    {
        $refund = parent::baseNew($staff_id);

        $refund->distributor_id = $distributor_id;
        $refund->save();
        return $refund;
    }

    public function isSupply(): bool
    {
        return !is_null($this->supply_id);
    }

    public function isArrival(): bool
    {
        return !is_null($this->arrival_id);
    }

    //*** GET-...

    public function getFounded(): string
    {
        if ($this->isSupply()) return $this->supply->number . ' от ' . $this->supply->htmlDate();
        if ($this->isArrival()) return $this->arrival->number . ' от ' . $this->arrival->htmlDate();
        return 'Нет основания';
    }

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

    public function getStorage()
    {
        if (!is_null($this->storage_id)) return $this->storage;
        if ($this->isArrival()) {
            if (is_null($this->storage_id)) {
                $this->storage_id = $this->arrival->storage_id;
                $this->save();
                $this->refresh();
            }
            return $this->storage;
        }
        return null;
    }

    public function storageName(): string
    {
        if (!is_null($storage = $this->getStorage())) return $storage->name;
        return '';
    }

    public function addProduct(AccountingProduct $item): void
    {
        $product = RefundProduct::baseNew($item->product_id, $item->quantity);
        if ($this->isSupply()) {
            $product->supply_product_id = $item->id;
            $this->products()->save($product);
            return;
        }

        if ($this->isArrival()) {
            $product->arrival_product_id = $item->id;
            $this->products()->save($product);
            return;
        }
        throw new \DomainException('Нет связанного документа. Нельзя добавить товар');
    }

    public function products(): HasMany
    {
        return $this->hasMany(RefundProduct::class, 'refund_id', 'id');
    }

    public function arrival(): BelongsTo
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function supply(): BelongsTo
    {
        return $this->belongsTo(SupplyDocument::class, 'supply_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributor_id', 'id');
    }



}
