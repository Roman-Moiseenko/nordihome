<?php

namespace App\Modules\Shop\Repository;

use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class ECommerceRepository
{

    public function getDataCommerce(Request $request): array
    {
        $type = $request->input('e_type');
        $id = $request->input('e_id');
        $quantity = $request->integer('quantity');

        $data = $this->getListProducts($id, $quantity);

        return [
            'ecommerce' => [
                'currencyCode' => 'RUB',
                $type => $data,
            ],
        ];
    }

    private function getListProducts(mixed $ids, int $quantity): array
    {
        $result = [];
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $result[] = $this->getProduct($id['id'], $id['quantity']);
            }
        } else {
            $result[] = $this->getProduct($ids, $quantity);
        }
        return $result;
    }

    private function getProduct(int $id, int $quantity): array
    {
        $product = Product::find($id);
        return [
            "id" => $product->code,
            "name" => $product->name,
            "price" => $product->getPrice(),
            "brand" => $product->brand->name,
            "category" => $product->category->getParentNames(),
            "quantity" => $quantity,
        ];
    }
}
