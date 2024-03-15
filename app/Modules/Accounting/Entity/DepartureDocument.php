<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int $id
 * @property string $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $completed
 * @property int $storage_id
 * @property Storage $storage
 * @property DepartureProduct[] $departureProducts
 */
class DepartureDocument extends Model implements MovementInterface
{
    protected $table = 'departure_documents';

    protected $fillable = [
        'storage_id',
        'number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $number, int $storage_id): self
    {
        return self::create([
            'number' => $number,
            'storage_id' => $storage_id,
            'completed' => false,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->departureProducts as $item) {
            if ($item->product_id == $product_id) return true;
        }
        return false;
    }

    public function completed()
    {
        $this->completed = true;
        $this->save();
    }

    public function departureProducts()
    {
        return $this->hasMany(DepartureProduct::class, 'departure_id', 'id');
    }

    public function storage()
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
}
