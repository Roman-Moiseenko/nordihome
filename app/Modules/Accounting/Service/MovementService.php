<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\MovementHasCompleted;
use App\Events\ThrowableHasAppeared;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageArrivalItem;
use App\Modules\Accounting\Entity\StorageDepartureItem;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class MovementService
{

    private StorageService $storages;

    public function __construct(StorageService $storages)
    {
        $this->storages = $storages;
    }

    public function create(array $request): MovementDocument
    {
        return MovementDocument::register(
            $request['number'] ?? '',
            (int)$request['storage_out'],
            (int)$request['storage_in'],
        );
    }

    public function activate(MovementDocument $document)
    {
        $storageOut = $document->storageOut;
        foreach ($document->movementProducts as $movementProduct) {
            $departureItem = StorageDepartureItem::new($movementProduct->product_id, $movementProduct->quantity, $movementProduct->id);
            $storageOut->departureItems()->save($departureItem);
        }
        $document->departure();
        //TODO Оповещаем склад
    }

    public function departure(MovementDocument $document)
    {
        $storageOut = $document->storageOut;
        $storageIn = $document->storageIn;
        // Удаляем товар из Storage и создаем StorageArrivalItem
        foreach ($document->movementProducts as $movementProduct) {
            //удаляем из Storage и StorageDepartureItem
            $departureItem = $movementProduct->departureItem;
            $storageOut->sub($departureItem->product, $departureItem->quantity);
            $departureItem->delete();
            //создаем StorageArrivalItem
            $arrivalItem = StorageArrivalItem::new($movementProduct->product_id, $movementProduct->quantity, $movementProduct->id);
            $storageIn->arrivalItems()->save($arrivalItem);
        }
        $document->arrival();
    }

    public function arrival(MovementDocument $document)
    {
        $storageIn = $document->storageIn;
        foreach ($document->movementProducts as $movementProduct) {
            $arrivalItem = $movementProduct->arrivalItem;
            $storageIn->add($arrivalItem->product, $arrivalItem->quantity);
            $arrivalItem->delete();//удаляем StorageArrivalItem


            if (!empty($document->order())) {
                //TODO Проверяем, есть ли в перемещении заказ, то Ставим в резерв
                foreach ($document->order()->items as $item) {
                    if (!$item->preorder) {

                    }
                }
            }
        }
        $document->completed();
        if (!empty($document->order())) {
            //TODO Если есть в перемещении заказ, Оповещаем менеджера
        }


    }
    /**
     * Создание заявки на перемещение при обработке заказа на недостающее кол-во
     * @param Order $order
     * @return MovementDocument[]|null
     */
    #[Deprecated]
    public function createByOrder(Order $order): ?array
    {
        $storageIn = $order->delivery->point;
        $emptyItems = [];
        //Создаем список недостающих товаров
        foreach ($order->items as $orderItem) {
            $free_product = $storageIn->getAvailable($orderItem->product);
            if ($free_product < $orderItem->quantity) {
                $emptyItems[] = [
                    'product' => $orderItem->product,
                    'quantity' => $orderItem->quantity - $free_product,
                ];
            }
        }

        if (empty($emptyItems)) return null; //Товара на точке выдачи хватает

        $movements = [];
        /** @var Storage[] $storages */
        $storages = Storage::where('id', '<>', $storageIn->id)->where('point_of_delivery', true)->get();
        foreach ($storages as $storage) { //Для каждого склада создаем перемещение, пока кол-во не опустеет
            $movement = MovementDocument::register(
                'По заказу ' . $order->htmlNum(),
                $storage->id,
                $storageIn->id,
            );
            $movement->order_id = $order->id;
            $movement->save();

            foreach ($emptyItems as $i => &$item) {
                $free_product = $storage->getAvailable($item['product']);
                if ($free_product >= $item['quantity']) {
                    $movement->addProduct($item['quantity'], $item['quantity']);
                  /*  $movement->movementProducts()->create([
                        'quantity' => $item['quantity'],
                        'product_id' => $item['product']->id,
                        'cost' => $item['product']->getLastPrice(),
                    ]);*/
                    unset($emptyItems[$i]);
                } else {
                    $movement->addProduct($item['quantity'], $free_product);
/*
                    $movement->movementProducts()->create([
                        'quantity' => $free_product,
                        'product_id' => $item['product']->id,
                        'cost' => $item['product']->getLastPrice(),
                        ]);*/
                    $item['quantity'] -= $free_product;
                }
            }
            $movements[] = $movement;
            if (empty($emptyItems)) return $movements;
        }

        throw new \DomainException('Нехватка товара для исполнения заказа!');
    }

    //TODO Перемещение менять нельзя
    #[Deprecated]
    public function update(Request $request, MovementDocument $movement): MovementDocument
    {
        //
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Менять данные нельзя');
        $movement->number = $request['number'] ?? '';
        $movement->storage_out = (int)$request['storage_out'];
        $movement->storage_in = (int)$request['storage_in'];
        $movement->save();

        return $movement;
    }

    public function destroy(MovementDocument $movement)
    {
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Удалять нельзя');
        $movement->delete();
    }

    public function add(MovementDocument $movement,array $request): MovementDocument
    {
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($request['product_id']);
        $free_quantity = $movement->storageOut->getAvailable($product);
        $quantity = min((int)$request['quantity'], $free_quantity);

        //Добавляем в документ
        $movement->addProduct($product, $quantity);
        $movement->refresh();
        return $movement;
    }

    //Для AJAX
    public function set(Request $request, MovementProduct $item)
    {
        if (!$item->document->isDraft()) throw new \DomainException('Документ в работе. Менять данные нельзя');
        //Меняем данные
        $item->quantity = (int)$request['quantity'];
        $item->save();
        return $item->document->getInfoData();
    }


    public function createByExpense(OrderExpense $expense, array $request):? MovementDocument
    {
        //TODO !!!!!
        //Проверяем наличие на складе
        //Если нехватает, создаем перемещение

        return null;
    }
}
