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

class MovementService // extends AccountingService
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

    public function departure(MovementDocument $document)
    {
        DB::transaction(function () use ($document) {
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
            $document->statusArrival();
        });

    }

    public function arrival(MovementDocument $document)
    {
        DB::transaction(function () use ($document) {
            $storageIn = $document->storageIn;
            foreach ($document->movementProducts as $movementProduct) {
                $arrivalItem = $movementProduct->arrivalItem;
                $storageIn->add($arrivalItem->product, $arrivalItem->quantity);
                $arrivalItem->delete();//удаляем StorageArrivalItem

                //Если перемещение под заказ, то резервируем
                if (!empty($document->order()))
                    $this->reserveService->ReserveWithMovement(
                        $document->storageOut, $document->storageIn,
                        $movementProduct->orderItem,
                        $movementProduct->quantity);
            }
            $document->statusCompleted();
            if (!empty($document->order())) { //Уведомляем менеджера, что товар поступил
                $document->order()->manager->notify(new StaffMessage('Перемещение товара по заказу', $document->order()->htmlNum()));
            }
        });
    }

    public function destroy(MovementDocument $movement): void
    {
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Удалять нельзя');
        $movement->delete();
    }

    public function addProduct(MovementDocument $movement, int $product_id, int $quantity = null): void
    {
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($product_id);
        if ($movement->isProduct($product_id)) {
            flash('Товар ' . $product->name . ' уже добавлен в документ', 'warning');

        }
        $free_quantity = $movement->storageOut->getAvailable($product);
        $quantity = min($quantity, $free_quantity);

        //Добавляем в документ
        $movement->addProduct($product, $quantity);
        $movement->refresh();

    }

    public function addProducts(MovementDocument $movement, array $products): void
    {
        foreach ($products as $product) {
            $_product = Product::whereCode($product['code'])->first();
            if (!is_null($_product)) {
                $this->addProduct($movement, $_product->id, (int)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }

    public function setProduct(Request $request, MovementProduct $product): void
    {
        if ($product->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        //Меняем данные
        //TODO Проверка на наличие на складе
        $product->quantity = $request->integer('quantity');
        $product->save();
    }

    public function setInfo(MovementDocument $movement, Request $request)
    {
        throw new \DomainException('В разработке');
    }

    public function completed(MovementDocument $movement)
    {
        throw new \DomainException('В разработке');
    }

    public function work(MovementDocument $movement)
    {
        throw new \DomainException('В разработке');
    }
}
