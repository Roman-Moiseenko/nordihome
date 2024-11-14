<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\ArrivalProduct;
use App\Modules\Accounting\Entity\RefundDocument;
use App\Modules\Accounting\Entity\RefundProduct;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class RefundService
{
    private StorageService $storageService;

    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function create(int $arrival_id): RefundDocument
    {
        $staff = \Auth::guard('admin')->user();
        $arrival = ArrivalDocument::find($arrival_id);

        return RefundDocument::register($staff->id, $arrival->id, $arrival->distributor_id, $arrival->storage_id);
    }

    public function completed(RefundDocument $refund): void
    {
        if (!is_null($refund->storage_id)) {
            $this->storageService->departure($refund->storage, $refund->products);// Списываем из склада
            $refund->completed();
            return;
        }
        throw new \DomainException('Не указан склад списания');
    }

    public function work(RefundDocument $refund): void
    {
        if (!is_null($refund->storage_id))
            $this->storageService->arrival($refund->storage, $refund->products);
        $refund->work();
    }

    public function setInfo(RefundDocument $refund, Request $request): void
    {
        $refund->baseSave($request->input('document'));
        $refund->storage_id = $request->input('storage_id');
        $refund->arrival_id = $request->input('arrival_id');
        $refund->save();
    }

    public function delete(RefundDocument $refund): void
    {
        if (!$refund->isCompleted()) $refund->delete();
    }

    public function addProduct(RefundDocument $refund, int $product_id, int $quantity): ?RefundProduct
    {
        if ($refund->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');
        $distributor_cost = 0;
        /** @var Product $product */
        $product = Product::find($product_id);
        /** @var ArrivalProduct $arrivalProduct */
        $arrivalProduct = $refund->arrival->getProduct($product->id);
        if (is_null($arrivalProduct)) throw new \DomainException('Товар ' . $product->name . ' отсутствует в связанном документе.');
        $quantity = min($quantity, $arrivalProduct->getQuantityUnallocated());
        if ($quantity <= 0)  throw new \DomainException('Недостаточное кол-во товара ' . $product->name . ' в связанном документе.');
        $distributor_cost = $arrivalProduct->cost_currency;

        //Если товар уже есть в Документе
        if ($refund->isProduct($product_id)) {
            $refund->getProduct($product_id)->addQuantity($quantity);
            return null;
        }
        //Добавляем в документ если его нет
        $item = RefundProduct::new(
            $product->id,
            $quantity,
            $distributor_cost,
        );
        $refund->products()->save($item);
        return $item;
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

    public function setProduct(RefundProduct $refundProduct, int $quantity): void
    {
        $refund = $refundProduct->document;
        if ($refund->isCompleted()) throw new \DomainException('Документ проведен. Менять данные нельзя');

        $unallocated = $refundProduct->getArrivalProduct()->getQuantityUnallocated();

        $delta = min($quantity - $refundProduct->quantity, $unallocated);
        if ($delta == 0) throw new \DomainException('Недостаточное кол-во товара ' . $refundProduct->product->name . ' в связанном документе.');
        $refundProduct->addQuantity($delta);
    }
}
