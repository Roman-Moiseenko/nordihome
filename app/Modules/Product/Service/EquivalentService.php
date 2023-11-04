<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Equivalent;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;

class EquivalentService
{

    public function register(Request $request): Equivalent
    {
        $equivalent = Equivalent::register($request['name'], (int)$request['category_id']);
        return $equivalent;
    }

    public function rename(Request $request, Equivalent $equivalent): Equivalent
    {
        $equivalent->update([
            'name' => $request['name'],
        ]);
        return $equivalent;
    }

    public function delete(Equivalent $equivalent)
    {
        $equivalent->products()->detach();
        Equivalent::destroy($equivalent->id);
    }

    public function add_product(Request $request, Equivalent $equivalent)
    {
        $id = (int)$request['product_id'];
        if(!$equivalent->isProduct($id)) $equivalent->products()->attach($id);
    }

    public function del_product(Product $product): Equivalent
    {
        $equivalent =$product->equivalent;
        $equivalent->products()->detach($product->id);
        return $equivalent;
    }
}
