<?php


namespace App\Modules\Product\Repository;


use App\Modules\Product\Entity\Product;

class ProductRepository
{
    /**
     * Репозиторий для передачи данных в другие модулю (шлюз)
     * Либо сделать Шлюз get + query
     * \Bus\FletchProduct и \Bus\QueryProduct
     * в QueryProduct setCountForSell(%id, %count) и если нет ТУ то setPriceForSell($id, $price)
     */



    public function getProductJSON(int $id): string
    {
        return json_encode([]);
    }

    public function forCommodity(int $id): string
    {
        /** @var Product $product */
        $product = Product::find($id);
        if (!empty($product)) {
            return json_encode(['code' => $product->code, 'name' => $product->name]);
        }
        return json_encode([]);
    }

    public function search(string $search)
    {
        $products = Product::orderBy('name')->where(function ($query) use ($search) {
            $query->where('code', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%");
        })->take(10)->get();
        return $products;
    }
}
