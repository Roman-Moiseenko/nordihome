<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Events\DepartureHasCompleted;
use App\Modules\Accounting\Entity\DepartureDocument;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartureService
{
    private StorageService $storages;

    public function __construct(StorageService $storages)
    {
        $this->storages = $storages;
    }

    public function create(int $storage_id, int $customer_id = null): DepartureDocument
    {
        /** @var Admin $manager */
        $manager = Auth::guard('admin')->user();
        $departure = DepartureDocument::register($storage_id, $manager->id);
        if (is_null($customer_id)) {
            $trader = Trader::default();
            $customer_id = $trader->organization->id;
        }
        $departure->customer_id = $customer_id;
        $departure->save();
        return $departure;
    }

    public function update(Request $request, DepartureDocument $departure): DepartureDocument
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $departure->number = $request->string('number')->trim()->value();
        $departure->storage_id = $request->integer('storage_id');
        $departure->save();

        return $departure;
    }

    public function destroy(DepartureDocument $departure): void
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Удалять нельзя');
        $departure->delete();
    }

    public function addProduct(DepartureDocument $departure, int $product_id, float $quantity): ?DepartureDocument
    {
        if ($departure->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        /** @var Product $product */
        $product = Product::find($product_id);

        $free_quantity = $departure->storage->getAvailable($product);
        if ($free_quantity <= 0) throw new \DomainException('Недостаточное кол-во товара ' . $product->name . ' на складе.');
        $quantity = min($quantity, $free_quantity);

        if ($departure->isProduct($product_id)) {
            $departureProduct = $departure->getProduct($product_id);
            $departureProduct->addQuantity($quantity);
            return null;
        }

        //Добавляем в документ
        $departure->products()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'cost' => $product->getPriceCost()
        ]);
        $departure->refresh();
        return $departure;
    }

    public function addProducts(DepartureDocument $departure, mixed $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($departure,
                $product['product_id'],
                $product['quantity'],
            );
        }
    }

    public function setProduct(Request $request, DepartureProduct $item): void
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        //Меняем данные
        $item->setQuantity($request->float('quantity'));
        $item->save();
    }

    public function completed(DepartureDocument $departure): void
    {
        if (is_null($departure->customer_id)) throw new \DomainException('Не указан заказчик');
        //Проведение документа
        DB::transaction(function () use ($departure) {
            $this->storages->departure($departure->storage, $departure->products);
            $departure->completed();
            event(new DepartureHasCompleted($departure));
        });
    }

    public function work(DepartureDocument $departure): void
    {
        DB::transaction(function () use ($departure) {
            $this->storages->arrival($departure->storage, $departure->products);
            $departure->work();
        });
    }

    public function setInfo(DepartureDocument $departure, Request $request): void
    {
        $departure->baseSave($request->input('document'));
        if ($departure->storage_id !== $request->integer('storage_id')) {
            //TODO Проверка на кол-во!
            $departure->storage_id = $request->integer('storage_id');
        }
        $departure->customer_id = $request->input('customer_id');
        $departure->save();
    }

    public function upload(DepartureDocument $departure, Request $request): void
    {
        $files = $request->file('files') ?? [];
        foreach ($files as $file) {
            $departure->photos()->save(Photo::upload(file: $file, thumb: false));
        }
    }

    public function deletePhoto(DepartureDocument $departure, Request $request)
    {
        $photo = Photo::find($request->integer('id'));
        $photo->delete();
    }
}
