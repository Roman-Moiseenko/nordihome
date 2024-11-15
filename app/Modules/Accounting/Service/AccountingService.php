<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\AccountingDocument;
use App\Modules\Product\Entity\Product;

abstract class AccountingService
{
    //abstract public function addProduct($document, int $product_id, int $quantity = null): void;

    public function addProducts(AccountingDocument $document, array $products): void
    {
        foreach ($products as $product) {
            $_product = Product::whereCode($product['code'])->first();
            if (!is_null($_product)) {
                $this->addProduct($document, $_product->id, (int)$product['quantity']);
            } else {
                $errors[] = $product['code'];
            }
        }
        if (!empty($errors)) throw new \DomainException('Не найдены товары ' . implode(', ', $errors));
    }
}
