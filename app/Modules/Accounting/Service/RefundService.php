<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\RefundDocument;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class RefundService
{

    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function create(int $distributor_id)
    {
        $staff = \Auth::guard('admin')->user();
        $refund = RefundDocument::register($staff->id, $distributor_id);
        return $refund;
    }

    public function completed(RefundDocument $refund): void
    {
        if ($refund->isSupply()) {
            $refund->completed();
            return;
        }
        if ($refund->isArrival()) {
            // Списываем из склада
            $this->storageService->departure($refund->storage, $refund->products);
            $refund->completed();
            return;
        }
        throw new \DomainException('Не указан склад списания');
    }

    public function work(RefundDocument $refund)
    {

    }

    public function setInfo(RefundDocument $refund, Request $request)
    {

    }

    public function delete(RefundDocument $refund): void
    {
        if (!$refund->isCompleted()) $refund->delete();
    }

    public function addProduct(RefundDocument $refund, int $integer, int $integer1)
    {
        //TODO Проверка связанного документа - наличие и кол-во
    }

    public function addProducts(RefundDocument $refund, mixed $products): void
    {
        $errors = [];
        foreach ($products as $product) {
            $product_id = Product::whereCode($product['code'])->first()->id;
            if (!is_null($product)) {
                $this->addProduct($refund, $product_id, (int)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }
}
