<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\LoggerService;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\OrderReserve;

class OrderReserveService
{
    private Storage $storage;
    private int $minutes;
    private LoggerService $logger;

    public function __construct(LoggerService $logger)
    {
        $this->storage = Storage::where('default', true)->first();
        $this->minutes = (new Options())->shop->reserve_order;
        $this->logger = $logger;
    }

    /**
     * Добавить в резерв при наличии свободных товаров по всем складам, начиная с Основного
     * @param OrderItem $orderItem
     * @param int $quantity
     * @return void
     */
    public function toReserve(OrderItem $orderItem, int $quantity)
    {
        if ($orderItem->product->getCountSell() < $quantity)
            throw new \DomainException('Нельзя поставить товар ' . $orderItem->product->name .
                ' в резерв, в наличии ' . $orderItem->product->getCountSell());
        $storageItem = $this->storage->getItem($orderItem->product);
        if ($storageItem->getFreeToSell() >= $quantity) {
            OrderReserve::register($orderItem->id, $storageItem->id, $quantity, $this->minutes);
        } else {
            OrderReserve::register($orderItem->id, $storageItem->id, $storageItem->getFreeToSell(), $this->minutes);
            $quantity -= $storageItem->getFreeToSell();
            foreach ($orderItem->product->storageItems as $_storageItem) {
                if ($quantity > 0 && $_storageItem->id != $storageItem->id) {
                    if ($quantity > $_storageItem->getFreeToSell()) {
                        OrderReserve::register($orderItem->id, $_storageItem->id, $_storageItem->getFreeToSell(), $this->minutes);
                        $quantity -= $_storageItem->getFreeToSell();
                    } else {
                        OrderReserve::register($orderItem->id, $_storageItem->id, $quantity, $this->minutes);
                        $quantity = 0;
                    }
                }
            }
        }
    }

    /**
     * Добавить в резерв в указанное хранилище (при поступлении)
     * @param OrderItem $orderItem
     * @param Storage $storage
     * @param int $quantity
     * @return void
     */
    public function toReserveStorage(OrderItem $orderItem, Storage $storage, int $quantity)
    {
        $storageItem = $storage->getItem($orderItem->product);
        OrderReserve::register($orderItem->id, $storageItem->id, $quantity, $this->minutes);
    }

    /**
     * Увеличение резерва при увеличении товара в заказе
     * @param OrderItem $orderItem
     * @param int $delta
     * @return void
     */
    public function upReserve(OrderItem $orderItem, int $delta)
    {
        foreach ($orderItem->product->storageItems as $storageItem) {
            $free_quantity = $storageItem->getFreeToSell();
            if ($free_quantity != 0) {//В данном хранилище есть товар
                if ($delta <= $free_quantity) {
                    $this->AddReserveOrderItem($orderItem, $storageItem, $delta);
                    return;
                } else {
                    $delta -= $free_quantity;
                    $this->AddReserveOrderItem($orderItem, $storageItem, $free_quantity);
                }
            }
        }
    }

    /**
     * Уменьшение резерва при уменьшении товара в заказе
     * @param OrderItem $orderItem
     * @param int $delta
     * @return void
     */
    public function downReserve(OrderItem $orderItem, int $delta)
    {
        foreach ($orderItem->reserves as $reserve) {
            if ($delta < $reserve->quantity) {
                $reserve->quantity -= $delta; //Уменьшение на $delta
                $reserve->save();
                return;
            } else {
                $delta -= $reserve->quantity;
                $reserve->delete();
            }
        }
    }

    public function delete(OrderReserve $reserve)
    {
        $reserve->delete();
    }

    private function AddReserveOrderItem(OrderItem $orderItem, StorageItem $storageItem, $add_quantity)
    {
        $reserve = $orderItem->getReserveByStorageItem($storageItem->id);
        if ($reserve != null) {
            $reserve->quantity += $add_quantity;
            $reserve->save();
        } else {
            OrderReserve::register($orderItem->id, $storageItem->id, $add_quantity, $this->minutes);
        }
    }

    /**
     * Перенос резерва при проведении перемещения
     * @param Storage $storageOut
     * @param Storage $storageIn
     * @param OrderItem $orderItem
     * @param int $quantity
     * @return void
     */
    public function ReserveWithMovement(Storage $storageOut, Storage $storageIn, OrderItem $orderItem, int $quantity)
    {
        $reserveOut = $orderItem->getReserveByStorage($storageOut->id);

        if ($reserveOut->quantity == $quantity) {
            $reserveOut->delete();
        } else {
            $reserveOut->quantity -= $quantity;
            $reserveOut->save();
        }

        $storageItemIn = $storageIn->getItem($orderItem->product);
        $this->AddReserveOrderItem($orderItem, $storageItemIn, $quantity);
    }


    /**
     * Собрать резерв для элемента заказа на одном складе
     * @param OrderItem $orderItem
     * @param int $storage_id
     * @param int $quantity
     * @return void
     */
    public function CollectReserve(OrderItem $orderItem, int $storage_id, int $quantity = 1)
    {
        /** @var Storage $storageIn */
        $storageIn = Storage::find($storage_id);
        $storageItem = $storageIn->getItem($orderItem->product);
        $storageItems = StorageItem::where('product_id', $orderItem->product_id)->where('id', '<>', $storageItem->id)->get(); //Остальные ячейки хранилища
        $quantity = min($quantity, $storageItem->getFreeToSell());

        /** @var StorageItem[] $storageItems */

        foreach ($storageItems as $item) {
            if (!is_null($reserve = $item->orderReserve($orderItem->order_id))) {

                if ($reserve->quantity <= $quantity) {
                    $this->AddReserveOrderItem($orderItem, $storageItem, $reserve->quantity); //Добавляем в резерв сколько есть
                    $quantity -= $reserve->quantity;
                    $reserve->delete();
                } else {
                    $this->AddReserveOrderItem($orderItem, $storageItem, $quantity);//Добавляем в резерв все
                    $reserve->quantity -= $quantity;
                    $reserve->save();
                    return;
                }
            }
        }
        $this->logger->logOrder($orderItem->order,
            'Перемещение резерва м/у складами',
            'Склад назначения ' . $storageIn->name,
            $quantity . ' шт.');
    }


}
