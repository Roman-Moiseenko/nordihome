<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\MovementHasCompleted;
use App\Events\ThrowableHasAppeared;
use App\Modules\Accounting\Entity\MovementDocument;
use App\Modules\Accounting\Entity\MovementProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementService
{

    private StorageService $storages;

    public function __construct(StorageService $storages)
    {
        $this->storages = $storages;
    }

    public function create(Request $request): MovementDocument
    {
        $movement = MovementDocument::register(
            $request['number'],
            (int)$request['storage_out'],
            (int)$request['storage_in'],
        );
        return $movement;
    }


    public function createByOrder(Order $order)
    {
        return null;

        $storageIn = $order->delivery->point;
        $emptyItems = [];
        //Создаем список недостающих товаров
        foreach ($order->items as $orderItem) {
            //TODO
        }

        $storages = Storage::where('id', '<>', $storageIn->id)->get();
        foreach ($storages as $storage) {

        }
        //Ищем в остальных хранилищах


        $movement = MovementDocument::register(
            'по заказу ' . $order->htmlNum(),
            1,
            $storageIn->id,
        );
        $movement->order_id = $order->id;
        $movement->save();

        return $movement;
    }


    public function update(Request $request, MovementDocument $movement)
    {
        if ($movement->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $movement->number = $request['number'] ?? '';
        $movement->storage_out = (int)$request['storage_out'];
        $movement->storage_in = (int)$request['storage_in'];
        $movement->save();

        return $movement;
    }

    public function destroy(MovementDocument $movement)
    {
        if ($movement->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $movement->delete();
    }

    public function add(Request $request, MovementDocument $movement)
    {
        if ($movement->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($request['product_id']);
        $free_quantity = $movement->storageOut->getQuantity($product) - $movement->storageOut->getReserve($product);
        $quantity = min((int)$request['quantity'], $free_quantity);

        //Добавляем в документ
        $movement->movementProducts()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
        $movement->refresh();
        return $movement;
    }

    //Для AJAX
    public function set(Request $request, MovementProduct $item): bool
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        //Меняем данные
        $item->quantity = (int)$request['quantity'];
        $item->save();
        return true;
    }

    public function completed(MovementDocument $movement)
    {
        //Проведение документа


        DB::beginTransaction();
        try {
            $this->storages->arrival($movement->storageIn, $movement->movementProducts()->getModels());
            $this->storages->departure($movement->storageOut, $movement->movementProducts()->getModels());
            $movement->completed();
            DB::commit();
            event(new MovementHasCompleted($movement));
        } catch (\Throwable $e) {
            DB::rollBack();
            flash($e->getMessage(), 'danger');
            event(new ThrowableHasAppeared($e));
        }
    }
}
