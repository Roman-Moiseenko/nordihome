<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Оприходование излишков
 * @property int $storage_id
 * @property int $customer_id
 *
 * @property Storage $storage
 * @property SurplusProduct[] $products
 * @property InventoryDocument $inventory
 * @property Organization $customer Заказчик
 */
class SurplusDocument extends AccountingDocument
{
    protected string $blank = 'Оприходование излишков';
    public $fillable = [
        'storage_id',
    ];

    public static function register(int $storage_id, int $staff_id): self
    {
        $surplus = parent::baseNew($staff_id);
        $surplus->storage_id = $storage_id;
        $surplus->save();
        return $surplus;
    }

    public function getAmount(): float
    {
        $amount = SurplusProduct::selectRaw('SUM(quantity * cost) AS total')
            ->where('surplus_id', $this->id)
            ->first();
        return (int)$amount->total ?? 0.0;
    }

    public function getQuantity(): float
    {
        $quantity = SurplusProduct::selectRaw('SUM(quantity * 1) AS total')
            ->where('surplus_id', $this->id)
            ->first();
        return (float)($quantity->total ?? 0);
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
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
    public function inventory(): HasOne
    {
        return $this->hasOne(InventoryDocument::class, 'surplus_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(SurplusProduct::class, 'surplus_id', 'id');
    }

    function documentUrl(): string
    {
        return route('admin.accounting.surplus.show', ['surplus' => $this->id], false);
    }

    public function onBased(): ?array
    {
        return null;
    }

    public function onFounded(): ?array
    {
        if (!is_null($this->inventory)) return $this->foundedGenerate($this->inventory);
        return null;
    }
}
