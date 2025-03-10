<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\SurplusDocument;
use App\Modules\Accounting\Entity\SurplusProduct;
use App\Modules\Accounting\Entity\Trader;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Product;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurplusService extends AccountingService
{
    private StorageService $storages;

    public function __construct(StorageService $storages)
    {
        $this->storages = $storages;
    }

    public function create_storage(int $storage_id, int $customer_id = null): SurplusDocument
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $surplus = SurplusDocument::register($storage_id, $staff->id);
        if (is_null($customer_id)) {
            $trader = Trader::default();
            $customer_id = $trader->organization->id;
        }
        $surplus->customer_id = $customer_id;
        $surplus->save();
        return $surplus;
    }

    public function work(SurplusDocument $surplus): void
    {
        DB::transaction(function () use ($surplus) {
            //Списываем со склада проведенные товары
            $this->storages->departure($surplus->storage, $surplus->products);

            $surplus->work();
        });
    }

    public function completed(SurplusDocument $surplus): void
    {
        DB::transaction(function () use ($surplus) {
            $this->storages->arrival($surplus->storage, $surplus->products);
            $surplus->completed();
        });
    }

    public function addProduct(SurplusDocument $surplus, int $product_id, float $quantity): ?SurplusDocument
    {
        if ($surplus->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        /** @var Product $product */
        $product = Product::find($product_id);

        if ($surplus->isProduct($product_id)) {
            $surplusProduct = $surplus->getProduct($product_id);
            $surplusProduct->addQuantity($quantity);
            return null;
        }

        //Добавляем в документ
        $surplus->products()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'cost' => $product->getPriceCost()
        ]);
        $surplus->refresh();
        return $surplus;
    }

    public function addProducts(SurplusDocument $surplus, mixed $products): void
    {
        foreach ($products as $product) {
            $this->addProduct($surplus,
                $product['product_id'],
                $product['quantity'],
            );
        }
    }

    public function setProduct(Request $request, SurplusProduct $item): void
    {
        if ($item->document->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        //Меняем данные
        $item->setQuantity($request->float('quantity'));
        $item->cost = $request->integer('cost');
        $item->save();
    }

    public function setInfo(SurplusDocument $surplus, Request $request): void
    {
        $surplus->baseSave($request->input('document'));
        if ($surplus->storage_id !== $request->integer('storage_id')) {
            //TODO Проверка на кол-во!
            $surplus->storage_id = $request->integer('storage_id');
        }
        $surplus->customer_id = $request->input('customer_id');

        $surplus->save();
    }
}
