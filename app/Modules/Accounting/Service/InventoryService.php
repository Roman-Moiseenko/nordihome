<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\DepartureProduct;
use App\Modules\Accounting\Entity\InventoryDocument;
use App\Modules\Accounting\Entity\InventoryProduct;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Product;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    private ArrivalService $arrivalService;
    private DepartureService $departureService;

    public function __construct(ArrivalService $arrivalService, DepartureService $departureService)
    {
        $this->arrivalService = $arrivalService;
        $this->departureService = $departureService;
    }

    public function create(int $storage_id): InventoryDocument
    {
        /** @var Admin $manager */
        $staff = Auth::guard('admin')->user();
        return InventoryDocument::register($storage_id, $staff->id);
    }


    public function completed(InventoryDocument $inventory): void
    {
        DB::transaction(function () use($inventory) {
            if (!is_null($inventory->surpluses)) { //Излишки
                $arrival = $this->arrivalService->create_storage($inventory->storage_id);
                foreach ($inventory->surpluses as $product) {
                    $item = ArrivalProduct::new(
                        $product->id,
                        $product->quantity - $product->formal,
                        $product->product->getPriceRetail(),
                    );
                    $arrival->products()->save($item);
                }
                $arrival->completed();
                $inventory->arrival_id = $arrival->id;
            }

            if (!is_null($inventory->shortages)) { //Недостача
                $departure = $this->departureService->create($inventory->storage_id);
                foreach ($inventory->shortages as $product) {
                    $item = DepartureProduct::new(
                        $product->id,
                        $product->formal - $product->quantity,
                        $product->product->getPriceRetail(),
                    );
                    $departure->products()->save($item);
                }
                $departure->completed();
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

            if (!is_null($inventory->arrival)) {
                $this->arrivalService->work($inventory->arrival);
                $this->arrivalService->destroy($inventory->arrival);
            }
            if (!is_null($inventory->departure)) {
                $this->departureService->work($inventory->departure);
                $this->departureService->destroy($inventory->departure);
            }
            $inventory->work();
        });
    }
    public function addProduct(InventoryDocument $inventory, int $product_id, int $quantity): void
    {
        $formal = $inventory->storage->getQuantity($product_id);
        $product = Product::find($product_id);
        $inventory_product = InventoryProduct::new($product_id, $quantity, $product->getPriceRetail(), $formal);
        $inventory->products()->save($inventory_product);
    }

    public function addProducts(InventoryDocument $inventory, array $products): void
    {
        $errors = [];
        foreach ($products as $product) {
            $_product = Product::whereCode($product['code'])->first();
            if (!is_null($_product)) {
                $this->addProduct($inventory, $_product->id, (int)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }

    public function setProduct(InventoryProduct $product, int $quantity): void
    {
        $product->setQuantity($quantity);
    }


}
