<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Traits\CompletedFieldModel;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

/**
 * Списание товаров
 * @property int $storage_id
 * @property int $customer_id
 *
 * @property Storage $storage
 * @property DepartureProduct[] $departureProducts
 * @property Admin $staff
 * @property InventoryDocument $inventory
 * @property Photo[] $photos
 * @property Organization $customer Заказчик
 */
class DepartureDocument extends AccountingDocument
{
    protected string $blank = 'Списание остатков';
    protected $table = 'departure_documents';

    protected $fillable = [
        'storage_id',
    ];

    public static function register(int $storage_id, int $staff_id): self
    {
        $departure = parent::baseNew($staff_id);
        $departure->storage_id = $storage_id;
        $departure->save();
        return $departure;
    }

    //GET-s...
    public function getAmount(): float
    {
        $amount = DepartureProduct::selectRaw('SUM(quantity * cost) AS total')
            ->where('departure_id', $this->id)
            ->first();
        return $amount->total ?? 0.0;
    }

    public function getQuantity(): float
    {
        $quantity = DepartureProduct::selectRaw('SUM(quantity * 1) AS total')
            ->where('departure_id', $this->id)
            ->first();
        return (float)($quantity->total ?? 0);
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    #[Deprecated]
    public function departureProducts(): HasMany
    {
        return $this->hasMany(DepartureProduct::class, 'departure_id', 'id');
    }

    //** RELATIONS */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'customer_id', 'id');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(InventoryDocument::class, 'departure_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(DepartureProduct::class, 'departure_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable');
    }


    function documentUrl(): string
    {
        return route('admin.accounting.departure.show', ['departure' => $this->id], false);
    }

    public function onBased(): ?array
    {
        return null;
    }

    public function onFounded(): ?array
    {
        return $this->foundedGenerate($this->inventory);
    }

}
