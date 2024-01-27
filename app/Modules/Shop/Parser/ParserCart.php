<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\ParserStorage;
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

        $this->merge_id_ui();

        $this->items = $this->load();
    }



    public function reload()
    {
        $this->items = $this->load();
        //TODO Считаем сумму доставки и общую сумму

    }


    public function add($product, $quantity = 1)
    {
        if (!empty($this->parserStorageQuery()->where('product_id', $product->id)->first())) {
            $this->plus($product, $quantity);
            return;
        }
        if (!is_null($this->user_id)) {
            ParserStorage::registerForUser($this->user_id, $product->id, $quantity);
        } else {
            ParserStorage::registerForGuest($this->user_ui, $product->id, $quantity);
        }
    }

    public function plus($product, $quantity)
    {
        $item = $this->parserStorageQuery()->where('product_id', $product->id)->first();
        if (empty($item)) {
            $this->add($product, $quantity);
        }
        $item->quantity += $quantity;
        $item->save();
    }

    public function sub($product, $quantity = 1)
    {
        $item = $this->parserStorageQuery()->where('product_id', $product->id)->first();
        $item->quantity -= $quantity;
        $item->save();
    }

    public function clear()
    {
        $items = $this->parserStorageQuery()->get();
        foreach ($items as $item) {
            $item->delete();
        }
        $this->items = [];
    }

    private function load(): array
    {
        return array_map(function (ParserStorage $storage) {
            /** @var ProductParser $product_parser */
            $product_parser = ProductParser::where('product_id', $storage->product_id)->first();

            return [
                'product' => $product_parser->product,
                'quantity' => $storage->quantity,
                'parser' => $product_parser,
            ];
        }, $this->parserStorageQuery()->getModels());
    }

    private function merge_id_ui()
    {
        /** @var ParserStorage[] $items */
        $items = $this->parserStorageQuery()->get();
        //Проверка есть ли для тек.пользователя товары по uuid
        if (!is_null($this->user_id) && !empty($uuid = Cookie::get('user_cookie_id'))) {
            /** @var ParserStorage[] $guest_items */
            $guest_items = ParserStorage::where('user_ui', $uuid)->get();
            if (!empty($guest_items)) {
                foreach ($guest_items as $guest_item) {
                    $_notDB = true;
                    foreach ($items as $item) {
                        if ($item->product_id == $guest_item->product_id) {
                            $this->plus($guest_item->product, $guest_item->quantity);
                            $_notDB = false;
                        }
                    }
                    if ($_notDB) $this->add($guest_item->product, $guest_item->quantity);
                    $guest_item->delete();
                }
            }
        }
    }

    private function parserStorageQuery()
    {
        if (is_null($this->user_id)) {
            return ParserStorage::where('user_ui', $this->user_ui);
        } else {
            return ParserStorage::where('user_id', $this->user_id);
        }
    }
}
