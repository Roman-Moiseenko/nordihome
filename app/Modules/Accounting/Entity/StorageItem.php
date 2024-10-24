<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Order\Entity\OrderReserve;
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
 * @property OrderReserve[] $orderReserves
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


/*
    public function inReserveMovement(int $order_id = null): int
    {
        $quantity = 0;

        foreach ($this->movementReserve as $item) {
            if (is_null($order_id)) {
                $quantity += $item->pivot->quantity;
            } else {
                $order = $item->document->order();
                if ($order->id == $order_id) {
                    $quantity += $item->pivot->quantity;
                }
            }
        }
        return $quantity;
    }*/

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

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }


    public function orderReserves()
    {
        return $this->hasMany(OrderReserve::class, 'storage_item_id', 'id');
    }


    public function orderReserve(int $order_id):? OrderReserve
    {
        foreach ($this->orderReserves as $orderReserve) {
            if ($orderReserve->orderItem->order_id == $order_id) return $orderReserve;
        }
        return null;
    }
    //Кол-во товара

    /**
     * Свободное кол-во для продажи на текущем складе, кроме заказа $order_id
     * @param int|null $order_id
     * @return int
     */
    public function getFreeToSell(int $order_id = null): int
    {
        return $this->quantity - $this->getQuantityReserve($order_id);
    }

    /**
     * Кол-во товара в резерве для текущего склада
     * @param int|null $order_id
     * @return int
     */
    public function getQuantityReserve(int $order_id = null): int
    {
        $quantity = 0;
        foreach ($this->orderReserves  as $orderReserve) {
            if ($order_id != $orderReserve->orderItem->order_id) $quantity += $orderReserve->quantity;
        }
        return $quantity;
    }

    public function getReserveByOrderItem(int $id)
    {
    }
}
