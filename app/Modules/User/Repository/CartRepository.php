<?php
declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class CartRepository
{

    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Product::orderBy('name')->Has('cartStorages')->OrHas('cartCookies');
        $filters = [];

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Product $product) => $this->ProductToArray($product));
    }

    private function ProductToArray(Product $product): array
    {
        return array_merge($product->toArray(), [
            'quantity' => $product->cartStorages->sum('quantity') + $product->cartCookies->sum('quantity'),
        ]);
    }
}
