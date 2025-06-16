<?php

namespace App\Modules\Exchange\Service;

use App\Modules\Product\Entity\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Exchange1CService
{

    public function __construct()
    {
    }

    public function authorization(string $key): bool
    {
        $header = explode(' ', $key);
        if ($header[0] != 'Bearer') return false;

        $bearer = env('BEARER_TOKEN', null);
        return $bearer == $header[1];
    }

    public function products(Request $request): array
    {
        $date = $request->input('date');

        $query = Product::OrderBy('created_at');
        if (!is_null($date)) {

           // dd(Carbon::parse($date));
            $field = 'updated_at';
            if ($request->has('create')) $field = 'created_at';
            $query->where($field, '>', Carbon::parse($date));
        }
        $data = $query->paginate(100)->withQueryString()
            ->through(fn(Product $product) => $this->ProductTo1C($product))->toArray();

        return [
            'products' => $data['data'],
            'pages' => $data['last_page'],
            'total' => $data['total'],
        ];
    }

    private function ProductTo1C(Product $product): array
    {
        return [
            "id" => $product->id,
            "name" => $product->name,
            "code" => $product->code,
            "buy" => $product->getPriceCost(),
            "sell" => $product->getPriceRetail(),
            "vat" => $product->VAT->name,
        ];
    }
}
