<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ParserCart //Repository
{
    /** @var ParserItem[] $items */
    public array $items;
    public int $delivery;
    public int $amount;

    private int|null $user_id = null;
    private string|null $user_ui = null;

    public function __construct()
    {
        if (Auth::guard('user')->check()) {
            $this->user_id = Auth::guard('user')->user()->id;
        } else {
            $this->user_ui = Cookie::get('user_cookie_id');
        }
    }
    public function load(): array
    {
        $items = $this->parserStorageQuery()->getModels();

        return array_map(function (Product $product) {
            $data = [];//$this->parsingCost($product->code_search);
            $product_parser = ProductParser::where('product_id', $product->id)->first();

            return [
                'product' => $product,
                'cost' => $data['cost'],
                'storages' => $data['storages'],
                'data' => $product_parser,
            ];
        }, $this->parserStorageQuery()->getModels());


    }
    public function loadItems()
    {
        $this->items = [];
    }

    public function add($product)
    {

    }

    public function clear()
    {

    }

    private function parserStorageQuery()
    {
        if (is_null($this->user_id)) {
            return ParserStorage::where('user_uuid', $this->user_ui);
        } else {
            return ParserStorage::where('user_id', $this->user_id);
        }
    }
}
