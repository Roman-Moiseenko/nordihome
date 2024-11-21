<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\AccountingProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StorageService
{

    public function create(Request $request): Storage
    {
        return Storage::register(
            $request->string('name')->trim()->value(),
        );
    }

    public function setInfo(Request $request, Storage $storage): void
    {

        $storage->name = $request->string('name')->trim()->value();
        $storage->slug = empty($request['slug']) ? Str::slug($request['name']) : $request['slug'];
        $storage->point_of_sale = $request->boolean('point_of_sale');
        $storage->point_of_delivery = $request->boolean('point_of_delivery');
        $storage->save();

        if ($request->has('address'))
            $storage->setAddress(
                $request->string('post')->trim()->value(),
                $request->string('city')->trim()->value(),
                $request->string('address')->trim()->value()
            );
        if ($request->has('latitude') && $request->has('longitude'))
            $storage->setCoordinate($request->float('latitude'), $request->float('longitude'));
        $this->photo($storage, $request->file('file'), $request->boolean('clear_file'));
        if ($request->has('default')) {
            Storage::where('default', true)->update(['default' => false]);
            $storage->setDefault();
        }

    }

    /**
     * Поступление товара списком
     * @param Storage $storage
     * @param AccountingProduct[] $items
     * @return void
     */
    public function arrival(Storage $storage, mixed $items): void
    {
        DB::transaction(function () use ($storage, $items) {
            foreach ($items as $item) {
                $product = $item->getProduct();
                $storage->add($product, $item->getQuantity());
            }
        });
    }

    /**
     * Списание товара списком
     * @param AccountingProduct[] $items
     * @return void
     */
    public function departure(Storage $storage, mixed $items): void
    {
        DB::transaction(function () use ($storage, $items) {
            foreach ($items as $item) {
                $product = $item->getProduct();
                $storage->sub($product, $item->getQuantity());
            }
        });

    }

    public function photo(Storage $storage, $file, bool $clear_current): void
    {
        if ($clear_current && (!is_null($storage->photo) || !is_null($storage->photo->file)))
            $storage->photo->delete();

        if (empty($file)) return;

        if (!empty($storage->photo)) {
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
            if (is_null($storage->getItem($product->id))) {
                $storage->add($product, 0);
            }
        }
    }

}
