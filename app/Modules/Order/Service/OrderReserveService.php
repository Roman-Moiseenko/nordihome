<?php
declare(strict_types=1);

namespace App\Modules\Order\Service;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Admin\Entity\Options;
use App\Modules\Analytics\LoggerService;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Entity\OrderReserve;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Support\Facades\DB;

class OrderReserveService
{
    private Storage $storage;
    private int $minutes;
    private LoggerService $logger;

    public function __construct(LoggerService $logger, SettingRepository $settings)
    {
        $this->storage = Storage::where('default', true)->first();
        $this->minutes = $settings->getCommon()->reserve;
        $this->logger = $logger;
    }

    /**
     * Добавить в резерв при наличии свободных товаров по всем складам, начиная с Основного
     */
    public function toReserve(OrderItem $orderItem, int $quantity): void
    {
        DB::transaction(function () use ($orderItem, $quantity) {
            if ($orderItem->product->getCountSell() < $quantity)
                throw new \DomainException('Нельзя поставить товар ' . $orderItem->product->name .
                    ' в резерв, в наличии ' . $orderItem->product->getCountSell());
            $storageItem = $this->storage->getItem($orderItem->product_id);
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
        });
    }

    /**
     * Добавить в резерв в указанное хранилище (при поступлении)
     */
    public function toReserveStorage(OrderItem $orderItem, Storage $storage, int $quantity): void
    {
        $storageItem = $storage->getItem($orderItem->product_id);
        OrderReserve::register($orderItem->id, $storageItem->id, $quantity, $this->minutes);
    }

    /**
     * Удалить из резерва при отмене поступления
     */
    public function deleteReserveStorage(OrderItem $orderItem, Storage $storage, int $quantity): void
    {
        $storageItem = $storage->getItem($orderItem->product_id);
        $orderReserve = OrderReserve::where('order_item_id', $orderItem->id)->where('storage_item_id', $storageItem->id)->first();
        if ($orderReserve->quantity == $quantity) {
            $orderReserve->delete();
        } else {
            $orderReserve->quantity -= $quantity;
            $orderReserve->save();
        }
    }

    /**
     * Увеличение резерва при увеличении товара в заказе
     */
    public function upReserve(OrderItem $orderItem, int $delta): void
    {
        DB::transaction(function () use ($orderItem, $delta) {
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
        });
    }

    /**
     * Уменьшение резерва при уменьшении товара в заказе
     */
    public function downReserve(OrderItem $orderItem, int $delta): void
    {
        DB::transaction(function () use ($orderItem, $delta) {
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
        });
    }

    public function delete(OrderReserve $reserve)
    {
        $reserve->delete();
    }

    private function AddReserveOrderItem(OrderItem $orderItem, StorageItem $storageItem, $add_quantity): void
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
     */
    public function ReserveWithMovement(Storage $storageOut, Storage $storageIn, OrderItem $orderItem, int $quantity): void
    {
        $reserveOut = $orderItem->getReserveByStorage($storageOut->id);

        if ($reserveOut->quantity == $quantity) {
            $reserveOut->delete();
        } else {
            $reserveOut->quantity -= $quantity;
            $reserveOut->save();
        }

        $storageItemIn = $storageIn->getItem($orderItem->product_id);
        $this->AddReserveOrderItem($orderItem, $storageItemIn, $quantity);
    }

    /**
     * Собрать резерв для элемента заказа на одном складе
     */
    public function CollectReserve(OrderItem $orderItem, int $storage_id, int $quantity = 1): void
    {
        /** @var Storage $storageIn */
        $storageIn = Storage::find($storage_id);
        $storageItem = $storageIn->getItem($orderItem->product_id);
        $storageItems = StorageItem::where('product_id', $orderItem->product_id)->where('id', '<>', $storageItem->id)->get(); //Остальные ячейки хранилища
        $quantity = min($quantity, $storageItem->getFreeToSell());
        if ($quantity == 0) throw new \DomainException('На складе получателя отсутствует товар для переноса резерва');

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
