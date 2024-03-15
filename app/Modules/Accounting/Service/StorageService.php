<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Entity\Photo;
use App\Modules\Accounting\Entity\MovementItemInterface;
use App\Modules\Accounting\Entity\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StorageService
{

    public function create(Request $request)
    {
        $storage = Storage::register(
            $request['organization_id'],
            $request['name'],
            $request->has('sale'),
            $request->has('delivery'),

        );
        if (!empty($request['address'])) $storage->setAddress($request['post'] ?? '', $request['city'] ?? '', $request['address']);

        if (!empty($request['latitude']) && !empty($request['longitude']))
            $storage->setCoordinate($request['latitude'], $request['longitude']);

        $this->photo($storage, $request->file('file'));

        return $storage;
    }

    public function update(Request $request, Storage $storage)
    {
        $storage->organization_id = $request['organization_id'];
        $storage->name = $request['name'];
        $storage->slug = empty($request['slug']) ? Str::slug($request['name']) : $request['slug'];
        $storage->point_of_sale = $request->has('sale');
        $storage->point_of_delivery = $request->has('delivery');
        $storage->save();

        if (!empty($request['address'])) $storage->setAddress($request['post'] ?? '', $request['city'] ?? '', $request['address']);

        if (!empty($request['latitude']) && !empty($request['longitude']))
            $storage->setCoordinate((float)$request['latitude'], (float)$request['longitude']);

        $this->photo($storage, $request->file('file'));

        return $storage;
    }


    /**
     * Поступление товара списком
     * @param MovementItemInterface[] $items
     * @return void
     */
    public function arrival(Storage $storage, array $items)
    {
        //Поступление товара, списком
        foreach ($items as $item) {
            $product = $item->getProduct();
            $storage->add($product, $item->getQuantity());
            $product->count_for_sell += $item->getQuantity();
            $product->save();
        }
    }
    /**
     * Списание товара списком
     * @param MovementItemInterface[] $items
     * @return void
     */
    public function departure(Storage $storage, array $items)
    {
        //Списание товара, списком
        foreach ($items as $item) {
            $product = $item->getProduct();
            $storage->sub($product, $item->getQuantity());
            $product->count_for_sell -= $item->getQuantity();
            $product->save();
        }
    }

    public function photo(Storage $storage, $file): void
    {
        if (empty($file)) return;
        if (!empty($brand->photo)) {
            $storage->photo->newUploadFile($file);
        } else {
            $storage->photo()->save(Photo::upload($file));
        }
        $storage->refresh();
    }

}
