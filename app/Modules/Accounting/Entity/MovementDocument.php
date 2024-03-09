<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property int $id
 * @property int $storage_out
 * @property int $storage_in
 * @property string $number

 * @property int $order_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $completed
 * @property string $comment Комментарий к документу, пока отключена, на будущее
 *
 * @property Storage $storageOut
 * @property Storage $storageIn
 * @property MovementProduct[] $movementProducts
 */
class MovementDocument extends Model implements MovementInterface
{
    protected $table = 'movement_documents';

    protected $fillable = [
        'storage_out',
        'storage_in',
        'number',
        'basic',
        'order_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $number, int $storage_out, int $storage_in): self
    {
        return self::create([
            'number' => $number,
            'storage_out' => $storage_out,
            'storage_in' => $storage_in,
            'completed' => false,
        ]);
    }

    public function completed()
    {
        $this->completed = true;
        $this->save();
    }


    public function movementProducts()
    {
        return $this->hasMany(MovementProduct::class, 'movement_id', 'id');
    }

    public function storageOut()
    {
        return $this->belongsTo(Storage::class, 'storage_out', 'id');
    }

    public function storageIn()
    {
        return $this->belongsTo(Storage::class, 'storage_in', 'id');
    }

    public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->movementProducts as $item) {
            if ($item->product_id == $product_id) return true;
        }
        return false;
    }
    #[ArrayShape([
        'quantity' => 'int',
        'cost' => 'float',
    ])]
    public function getInfoData(): array
    {
        $quantity = 0;
        $cost = 0;
        foreach ($this->movementProducts as $item) {
            $quantity += $item->quantity;
            $cost += $item->quantity * $item->product->lastPrice->value;
        }
        return [
            'quantity' => $quantity,
            'cost' => $cost,
        ];
    }
}
