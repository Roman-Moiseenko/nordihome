<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Entity\Photo;
use App\Modules\Accounting\Entity\MovementItemInterface;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Product\Entity\Product;
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
            $request->has('delivery')
        );
        if (!empty($request['address'])) $storage->setAddress($request['post'] ?? '', $request['city'] ?? '', $request['address']);
        if (!empty($request['latitude']) && !empty($request['longitude']))
            $storage->setCoordinate($request['latitude'], $request['longitude']);
        $this->photo($storage, $request->file('file'));
        if (!empty($request['default'])) {
            Storage::where('default', true)->update(['default' => false]);
            $storage->setDefault();
        }
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

        if (!empty($request['default'])) {
            Storage::where('default', true)->update(['default' => false]);
            $storage->setDefault();
        }
        if (empty($request['default']) && $storage->default == true) {
            flash('Склад по умолчанию должен быть назначен! Выберите нужный склад, и сделайте его по умолчанию.', 'warning');
        }

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
            $product->setCountSell($product->getCountSell() + $item->getQuantity());
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
            $product->setCountSell($product->getCountSell() - $item->getQuantity());
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

    /**
     * Добавление товара во все имеющиеся хранилища (с кол-вом 0), если его еще нет (для нового)
     * @param Product $product
     * @return void
     */
    public function add_product(Product $product)
    {
        /** @var Storage[] $storages */
        $storages = Storage::get();
        foreach ($storages as $storage) {
            if (is_null($storage->getItem($product))) {
                $storage->add($product, 0);
            }
        }
    }

}
