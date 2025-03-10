<?php

namespace App\Modules\Accounting\Entity;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $surplus_id
 * @property int $departure_id
 * @property int $storage_id
 * @property int $customer_id
 *
 * @property SurplusDocument $surplus
 * @property DepartureDocument $departure
 * @property Storage $storage
 * @property Organization $customer Заказчик
 *
 * @property InventoryProduct[] $surpluses
 * @property InventoryProduct[] $shortages
 */
class InventoryDocument extends AccountingDocument
{
    protected string $blank = 'Инвентаризация';

    public static function register(int $storage_id, int $staff_id): self
    {
        $inventory = parent::baseNew($staff_id);
        $inventory->storage_id = $storage_id;
        $inventory->save();
        return $inventory;
    }

    public function getAmount(): float|int
    {
        $amount = 0;
        /** @var InventoryProduct $product */
        foreach ($this->products as $product) {
            $amount += $product->cost * ($product->formal - $product->quantity);
        }
        return $amount;
    }

    public function getFormalAmount(): float|int
    {
        $amount = 0;
        /** @var InventoryProduct $product */
        foreach ($this->products as $product) {
            $amount += $product->cost * $product->formal;
        }
        return $amount;
    }

    public function getActuallyAmount(): float|int
    {
        $amount = 0;
        /** @var InventoryProduct $product */
        foreach ($this->products as $product) {
            $amount += $product->cost * $product->quantity;
        }
        return $amount;
    }

    public function getSurplusAmount(): float|int
    {
        $amount = 0;
        foreach ($this->surpluses as $product) {
            $amount += $product->cost * ($product->formal - $product->quantity);
        }
        return $amount;
    }

    public function getShortagesAmount(): float|int
    {
        $amount = 0;
        foreach ($this->shortages as $product) {
            $amount += $product->cost * ($product->formal - $product->quantity);
        }
        return $amount;
    }

    //** RELATIONS */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'customer_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function surplus(): BelongsTo
    {
        return $this->belongsTo(SurplusDocument::class, 'surplus_id', 'id')->withTrashed();
    }

    public function departure(): BelongsTo
    {
        return $this->belongsTo(DepartureDocument::class, 'departure_id', 'id')->withTrashed();
    }

    /** Излишки */
    public function surpluses(): HasMany
    {
        return $this->products()->whereRaw('formal < quantity');
    }

    /** Недостача */
    public function shortages(): HasMany
    {
        return $this->products()->whereRaw('formal > quantity');
    }

    public function products(): HasMany
    {
        return $this->hasMany(InventoryProduct::class, 'inventory_id', 'id');
    }

    function documentUrl(): string
    {
        return route('admin.accounting.inventory.show', ['inventory' => $this->id]);
    }

    public function onBased(): ?array
    {
        $array = [];
        if (!is_null($this->surplus())) $array[] = $this->basedItem($this->surplus);
        if (!is_null($this->departure)) $array[] = $this->basedItem($this->departure);
        return $array;
    }

    public function onFounded(): ?array
    {
        return null;
    }

    public function delete(): void
    {
        if (!is_null($this->surplus)) $this->surplus->delete();
        if (!is_null($this->departure)) $this->departure->delete();

        parent::delete();
    }

    public function restore(): void
    {
        parent::restore();
        if (!is_null($this->surplus)) $this->surplus->restore();
        if (!is_null($this->departure)) $this->departure->restore();

    }

    public function forceDelete(): void
    {
        if (!is_null($this->surplus)) $this->surplus->forceDelete();
        if (!is_null($this->departure)) $this->departure->forceDelete();

        parent::forceDelete();
    }
}
