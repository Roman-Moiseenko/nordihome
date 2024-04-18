<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $storage_id
 * @property int $product_id
 * @property int $quantity
 *
 * @property Product $product
 * @property MovementProduct $movementReserve
 * @property Storage $storage
 */
class StorageItem extends Model
{
    protected $table = 'storage_items';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'quantity'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * В резерве на текущем складе
     * @return int
     */
    public function inReserve(): int
    {
        return $this->product->reserves()->where('storage_id', $this->storage_id)->sum('quantity');
    }

    public function inReserveMovement(int $order_id = null): int
    {
        $quantity = 0;
        /** @var MovementProduct $item */
        foreach ($this->movementReserve as $item) {
            if (is_null($order_id)) {
                $quantity += $item->pivot->quantity;
            } else {
                $order = $item->document->order();
                if ($order->id != $order_id) {
                    $quantity += $item->pivot->quantity;
                }
            }
        }
        return $quantity;
    }

    public function getDeparture(): int
    {
        return StorageDepartureItem::where('storage_id', $this->storage_id)->where('product_id', $this->product_id)->pluck('quantity')->sum();
    }

    public function getArrival(): int
    {
        return StorageArrivalItem::where('storage_id', $this->storage_id)->where('product_id', $this->product_id)->pluck('quantity')->sum();
    }

    /**
     * Хелпер для списка товаров в Хранилище - движение (-Убытие, +Поступление)
     * @return string
     */
    public function inMovementHTML(): string
    {
        $result = '-';
        if (($departure = $this->getDeparture()) != 0) $result = '-' . $departure;
        if (($arrival = $this->getArrival()) != 0) $result = '+' . $arrival;

        return $result;
    }

    public function sub(int $quantity)
    {
        $this->quantity -= $quantity;
        $this->save();
    }

    public function add(int $quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }

    public function movementReserve()
    {
        return $this->belongsToMany(MovementProduct::class, 'storages_movements', 'storage_item_id', 'movement_item_id')->withPivot(['quantity']);
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

}
