<?php
declare(strict_types=1);

namespace App\Modules\User\Repository;

use App\Modules\Product\Entity\Product;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class WishRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = Product::orderBy('name')->Has('wishes');
        $filters = [];

        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(Product $product) => $this->ProductToArray($product));
    }

    private function ProductToArray(Product $product): array
    {
        return array_merge($product->toArray(), [
            'quantity' => $product->wishes->count(),
        ]);
    }
}
