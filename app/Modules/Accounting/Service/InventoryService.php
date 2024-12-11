<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Accounting\Entity\InventoryDocument;
use App\Modules\Accounting\Entity\InventoryProduct;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\SurplusProduct;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Product;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Deprecated;

class InventoryService
{
    private DepartureService $departureService;
    private SurplusService $surplusService;

    public function __construct(DepartureService $departureService, SurplusService $surplusService)
    {
        $this->departureService = $departureService;
        $this->surplusService = $surplusService;
    }

    public function create(int $storage_id): InventoryDocument
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $storage = Storage::find($storage_id);
        $inventory = InventoryDocument::register($storage_id, $staff->id);

        foreach ($storage->items as $item) {
            $inventory_product = InventoryProduct::new(
                $item->product->id,
                0,
                $item->product->getPriceCost(),
                $item->quantity
            );
            $inventory->products()->save($inventory_product);
        }
        return $inventory;
    }


    public function completed(InventoryDocument $inventory): void
    {
        DB::transaction(function () use($inventory) {
            if ($inventory->surpluses()->count() > 0) { //Излишки
                $surplus = $this->surplusService->create_storage($inventory->storage_id);

                foreach ($inventory->surpluses as $product) {
                    $item = SurplusProduct::new(
                        $product->product_id,
                        $product->quantity - $product->formal,
                        $product->cost,
                    );
                    $surplus->products()->save($item);
                }
                $this->surplusService->completed($surplus);

                $inventory->surplus_id = $surplus->id;
            }

            if ($inventory->shortages()->count() > 0) { //Недостача
                $departure = $this->departureService->create($inventory->storage_id);
                foreach ($inventory->shortages as $product) {
                    $item = DepartureProduct::new(
                        $product->product_id,
                        $product->formal - $product->quantity,
                        $product->cost,
                    );
                    $departure->products()->save($item);
                }
                $this->departureService->completed($departure);
                $inventory->departure_id = $departure->id;
            }

            $inventory->completed();
        });
    }

    public function setInfo(InventoryDocument $inventory, Request $request): void
    {
        $inventory->baseSave($request->input('document'));
        $inventory->save();
    }

    public function destroy(InventoryDocument $inventory): void
    {
        if ($inventory->isCompleted()) throw new \DomainException('Нельзя удалить проведенный документ');
        $inventory->delete();
    }

    public function work(InventoryDocument $inventory): void
    {
        DB::transaction(function () use($inventory) {

            if (!is_null($inventory->surplus)) {
                $this->surplusService->work($inventory->surplus);
                $this->surplusService->destroy($inventory->surplus);
            }
            if (!is_null($inventory->departure)) {
                $this->departureService->work($inventory->departure);
                $this->departureService->destroy($inventory->departure);
            }
            $inventory->work();
        });
    }

    #[Deprecated]
    public function addProduct(InventoryDocument $inventory, int $product_id, float $quantity): void
    {
        $formal = $inventory->storage->getQuantity($product_id);
        $product = Product::find($product_id);
        $inventory_product = InventoryProduct::new($product_id, $quantity, $product->getPriceRetail(), $formal);
        $inventory->products()->save($inventory_product);
    }

    #[Deprecated]
    public function addProducts(InventoryDocument $inventory, array $products): void
    {
        $errors = [];
        foreach ($products as $product) {
            $_product = Product::whereCode($product['code'])->first();
            if (!is_null($_product)) {
                $this->addProduct($inventory, $_product->id, (float)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }

    public function setProduct(InventoryProduct $product, float $quantity): void
    {
        $product->setQuantity($quantity);

    }


}
