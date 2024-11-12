<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Traits\CompletedFieldModel;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

/**
 * Списание товаров
 * @property int $storage_id
 * @property Storage $storage
 * @property DepartureProduct[] $departureProducts
 * @property Admin $staff
 */
class DepartureDocument extends AccountingDocument implements AccountingDocumentInterface
{
    protected $table = 'departure_documents';

    protected $fillable = [
        'storage_id',
        'comment',
        'staff_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $storage_id, int $staff_id): self
    {
        $departure = parent::baseNew($staff_id);
        $departure->storage_id = $storage_id;
        $departure->save();
        return $departure;
    }


    #[Deprecated]
    public function departureProducts(): HasMany
    {
        return $this->hasMany(DepartureProduct::class, 'departure_id', 'id');
    }


    public function products(): HasMany
    {
        return $this->hasMany(DepartureProduct::class, 'departure_id', 'id');
    }

    public function storage(): BelongsTo
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    #[ArrayShape([
        'quantity' => 'int',
        'cost' => 'float',
    ])]
    public function getInfoData(): array
    {
        $quantity = 0;
        $cost = 0;
        foreach ($this->departureProducts as $item) {
            $quantity += $item->quantity;
            $cost += $item->quantity * ($item->cost ?? 0);
        }
        return [
            'quantity' => $quantity,
            'cost' => $cost,
        ];
    }


    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

}
