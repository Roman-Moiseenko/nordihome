<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\MovementHasCompleted;
use App\Events\ThrowableHasAppeared;
use App\Modules\Accounting\Entity\AccountingDocument;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageArrivalItem;
use App\Modules\Accounting\Entity\StorageDepartureItem;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\OrderReserveService;
use App\Modules\Product\Entity\Product;
use App\Notifications\StaffMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

class MovementService extends AccountingService
{

    private StorageService $storages;
    private OrderReserveService $reserveService;

    public function __construct(StorageService $storages, OrderReserveService $reserveService)
    {
        $this->storages = $storages;
        $this->reserveService = $reserveService;
    }

    public function create(int $storage_out, int $storage_in, int $arrival_id = null): MovementDocument
    {
        if ($storage_out == $storage_in) throw new \DomainException('Склады совпадают');

        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();

        return MovementDocument::register(
            $storage_out,
            $storage_in,
            $manager->id,
            $arrival_id
        );
    }

    public function activate(MovementDocument $document): void
    {
        DB::transaction(function () use ($document) {
            $storageOut = $document->storageOut;
            foreach ($document->movementProducts as $movementProduct) {
                $departureItem = StorageDepartureItem::new($movementProduct->product_id, $movementProduct->quantity, $movementProduct->id);
                $storageOut->departureItems()->save($departureItem);
            }
            $document->statusDeparture();
           // $document->setNumber();
        });
    }

    public function departure(MovementDocument $document): void
    {
        DB::transaction(function () use ($document) {
            $storageOut = $document->storageOut;
            $storageIn = $document->storageIn;
            // Удаляем товар из Storage и создаем StorageArrivalItem

            foreach ($document->movementProducts as $movementProduct) {
                //удаляем из Storage и StorageDepartureItem
                //dd($movementProduct->departureItem);
                $departureItem = $movementProduct->departureItem;
                $storageOut->sub($departureItem->product, (float)$departureItem->quantity);
                $departureItem->delete();
                //создаем StorageArrivalItem
                $arrivalItem = StorageArrivalItem::new($movementProduct->product_id, (float)$movementProduct->quantity, $movementProduct->id);
                $storageIn->arrivalItems()->save($arrivalItem);
            }
            $document->statusArrival();
        });

    }

    public function arrival(MovementDocument $document): void
    {
        DB::transaction(function () use ($document) {
            $storageIn = $document->storageIn;
            foreach ($document->movementProducts as $movementProduct) {
                $arrivalItem = $movementProduct->arrivalItem;
                $storageIn->add($arrivalItem->product, (float)$arrivalItem->quantity);
                $arrivalItem->delete();//удаляем StorageArrivalItem

                //Если перемещение под заказ, то резервируем
                if (!is_null($document->order))
                    $this->reserveService->ReserveWithMovement(
                        $document->storageOut, $document->storageIn,
                        $movementProduct->orderItem,
                        (float)$movementProduct->quantity);
            }
            $document->statusCompleted();
            if (!is_null($document->order)) { //Уведомляем менеджера, что товар поступил
                $document->order->staff->notify(new StaffMessage('Перемещение товара по заказу', $document->order->htmlNum()));
            }
        });
    }

    protected function isDeletable(MovementDocument|AccountingDocument $document): bool
    {
        return !$document->isDraft();
    }

    public function addProduct(MovementDocument $movement, int $product_id, float $quantity = null): void
    {
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($product_id);
        if ($movement->isProduct($product_id)) {
            throw new \DomainException('Товар ' . $product->name . ' уже добавлен в документ');

        }
        $movement->addProduct($product->id, $quantity);
    }

    public function addProducts(MovementDocument $movement, array $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($movement,
                $product['product_id'],
                $product['quantity'],
            );
        }
    }

    public function setProduct(Request $request, MovementProduct $product): void
    {
        if ($product->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        //Меняем данные
        $product->setQuantity($request->float('quantity'));
        $product->save();
    }

    public function setInfo(MovementDocument $movement, Request $request): void
    {
        $movement->baseSave($request->input('document'));
        $movement->storage_out = $request->integer('storage_out');
        $movement->storage_in = $request->integer('storage_in');
        if ($movement->storage_out == $movement->storage_in) throw new \DomainException('Склады совпадают');
        $movement->save();
    }

    public function completed(MovementDocument $movement): void
    {
        if ($movement->storage_out == $movement->storage_in) throw new \DomainException('Склады совпадают');

        foreach ($movement->products as $product) {
            $available = $movement->storageOut->getAvailable($product->product);
            if ($movement->order) { //Если товар переносится под Заказ, учитываем кол-во в заказе
                $available += $movement->order->getQuantityProduct($product->product_id, false);
            }
            if ($available < $product->quantity)
                throw new \DomainException('Недостаточно товара на складе убытия!');
        }
        DB::transaction(function () use ($movement) {
            $storageOut = $movement->storageOut;
            foreach ($movement->products as $movementProduct) {
                $departureItem = StorageDepartureItem::new($movementProduct->product_id, (float)$movementProduct->quantity, $movementProduct->id);
                $storageOut->departureItems()->save($departureItem);

            }
            $movement->statusDeparture();
            $movement->completed();
        });
    }

    public function work(MovementDocument $movement): void
    {
        if ($movement->isDeparture()) {
            DB::transaction(function () use ($movement) {
                foreach ($movement->products as $movementProduct) {
                    $movementProduct->departureItem->delete();
                }
                $movement->status == MovementDocument::STATUS_DRAFT;
                $movement->work();
            });
        } else {
            throw new \DomainException('Перемещение в пути, отменить нельзя');
        }
    }
}
