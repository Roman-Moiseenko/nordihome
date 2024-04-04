<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\DepartureHasCompleted;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class DepartureService
{
    private StorageService $storages;

    public function __construct(StorageService $storages)
    {
        $this->storages = $storages;
    }

    public function create(Request $request): DepartureDocument
    {
        return DepartureDocument::register(
            $request['number'],
            (int)$request['storage_id'],
        );
    }

    public function update(Request $request, DepartureDocument $departure): DepartureDocument
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $departure->number = $request['number'] ?? '';
        $departure->storage_id = (int)$request['storage_id'];
        $departure->save();

        return $departure;
    }

    public function destroy(DepartureDocument $departure)
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $departure->delete();
    }

    public function add(Request $request, DepartureDocument $departure): DepartureDocument
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($request['product_id']);
        $free_quantity = $departure->storage->getAvailable($product);
        $quantity = min((int)$request['quantity'], $free_quantity);

        //Добавляем в документ
        $departure->departureProducts()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'cost' => $product->lastPrice->value
        ]);
        $departure->refresh();
        return $departure;
    }

    //Для AJAX
    public function set(Request $request, DepartureProduct $item): array
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        //Меняем данные
        $item->quantity = (int)$request['quantity'];
        $item->save();
        return $item->document->getInfoData();
    }

    public function completed(DepartureDocument $departure)
    {
        //Проведение документа
        $this->storages->departure($departure->storage, $departure->departureProducts()->getModels());
        $departure->completed();
        event(new DepartureHasCompleted($departure));
    }
}
