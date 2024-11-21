<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\StorageItem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class StorageRepository extends AccountingRepository
{
    /**
     * Список хранилищ, где выдают товар
     * @return Storage[]
     */
    public function getPointDelivery(): array
    {
        return Storage::where('point_of_delivery', true)->getModels();
    }

    /**
     * Список хранилищ, где продают товар
     * @return Storage[]
     */
    public function getPointSale(): array
    {
        return Storage::where('point_of_sale', true)->getModels();
    }

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $filters = [];
        $query = Storage::orderBy('name');
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Storage $storage) => $this->StorageToArray($storage));

    }

    private function StorageToArray(Storage $storage): array
    {
        return array_merge($storage->toArray(), [
            'quantity' => $storage->getQuantity(),
            'image' => $storage->getImage(),
        ]);
    }

    public function StorageWithToArray(Storage $storage, Request $request): array
    {
        return array_merge($this->StorageToArray($storage), [
            'items' => $storage->items()
                ->paginate($request->input('size', 20))
                ->withQueryString()
                ->through(fn(StorageItem $item) => [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'movement' => $item->inMovementHTML(),
                    'reserve' => $item->getQuantityReserve(),
                    'cell' => $item->cell,
                    'product' => [
                        'id' => $item->product_id,
                        'name' => $item->product->name,
                        'code' => $item->product->code,
                        'category' => $item->product->category->name,
                        'for_sell' => $item->product->getCountSell(),
                    ],
                ]),
        ]);
    }
}
