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

class MovementService
{

    private StorageService $storages;
    private OrderReserveService $reserveService;

    public function __construct(StorageService $storages, OrderReserveService $reserveService)
    {
        $this->storages = $storages;
        $this->reserveService = $reserveService;
    }

    public function create(array $request): MovementDocument
    {
        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();

        $storage_out = (int)$request['storage_out'];
        $storage_in = (int)$request['storage_in'];

        /*$movement = MovementDocument::where('storage_out', $storage_out)->where('storage_in', $storage_in)->where('status', MovementDocument::STATUS_DRAFT)->first();
        if (!empty($movement)) return $movement;*/

        return MovementDocument::register(
            $storage_out,
            $storage_in,
            $request['comment'] ?? '',
            $manager->id
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
        $document->setNumber();
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

            //Если перемещение под заказ, то резервируем
            if (!empty($document->order()))
                $this->reserveService->ReserveWithMovement(
                    $document->storageOut, $document->storageIn,
                    $movementProduct->orderItem,
                    $movementProduct->quantity);
        }
        $document->completed();
        if (!empty($document->order())) { //Уведомляем менеджера, что товар поступил
            $document->order()->manager->notify(new StaffMessage('Перемещение товара по заказу', $document->order()->htmlNum()));
        }

    }

    public function destroy(MovementDocument $movement)
    {
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Удалять нельзя');
        $movement->delete();
    }

    public function add(MovementDocument $movement, int $product_id, int $quantity):? MovementDocument
    {
        if (!$movement->isDraft()) throw new \DomainException('Документ в работе. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($product_id);
        if ($movement->isProduct($product_id)) {
            flash('Товар ' . $product->name . ' уже добавлен в документ', 'warning');
            return null;
        }
        $free_quantity = $movement->storageOut->getAvailable($product);
        $quantity = min($quantity, $free_quantity);

        //Добавляем в документ
        $movement->addProduct($product, $quantity);
        $movement->refresh();
        return $movement;
    }

    public function add_products(MovementDocument $movement, string $textarea): void
    {
        $list = explode("\r\n", $textarea);
        foreach ($list as $item) {
            $product = Product::whereCode($item)->first();
            if (!is_null($product)) {
                $this->add($movement, $product->id, 1);
            } else {
                flash('Товар с артикулом ' . $item . ' не найден', 'danger');
            }
        }
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
}
