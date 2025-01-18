<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Repository\SettingRepository;
use App\Modules\User\Entity\ParserStorage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ParserCart //Repository
{
    /** @var ParserItem[] $items */
    public array $items;
    public int $delivery;
    public int $amount;
    public int $weight;

    private int|null $user_id = null;
    private string|null $user_ui = null;
    private Parser $parser;


    public function __construct(SettingRepository $settings)
    {
        $this->parser = $settings->getParser();
    }

    public function load(string $user_ui = '')
    {
        if (Auth::guard('user')->check()) {
            $this->user_id = Auth::guard('user')->user()->id;
        } else {
            $this->user_ui = Cookie::get('user_cookie_id');
            if (empty($this->user_ui)) {
                $this->user_ui = $user_ui;
            }
        }
        $this->merge_id_ui();
        $this->reload();
    }

    public function getItems(): array
    {
        $this->load();
        return $this->items;
    }

    public function reload()
    {
        $this->items = $this->loadItems();
        //Считаем сумму доставки и общую сумму
        $amount = 0;
        $weight = 0;
        /** @var ParserItem $item */
        foreach ($this->items as $item) {
            $amount += $item->cost * $item->quantity;
            $weight += (int)$item->product->weight() * $item->quantity;
        }
        $this->weight = $weight;
        $this->delivery = max($this->getCostDelivery($weight) * $weight, $this->parser->parser_delivery);
        $this->amount = $amount;
    }

    private function getCostDelivery(float $weight): int
    {
        foreach (ParserService::DELIVERY_PERIOD as $item) {
            if ($item['min'] < $weight & $weight <= $item['max']) {
                return (int)$item['value'];
            }
        }
        return $this->parser->parser_delivery;
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
        if ($item->quantity == 1) return;
        $item->quantity -= $quantity;
        $item->save();
    }

    public function set(Product $product, int $quantity)
    {
        $item = $this->parserStorageQuery()->where('product_id', $product->id)->first();
        $item->quantity = $quantity;
        $item->save();
    }

    public function remove(Product $product)
    {
        $item = $this->parserStorageQuery()->where('product_id', $product->id)->first();
        $item->delete();
    }

    public function clear()
    {
        $items = $this->parserStorageQuery()->get();
        foreach ($items as $item) {
            $item->delete();
        }
        $this->items = [];
        $this->delivery = 1000;
        $this->amount = 0;
    }

    private function loadItems(): array
    {
        return array_map(function (ParserStorage $storage) {
            /** @var ProductParser $product_parser */
            $product_parser = ProductParser::where('product_id', $storage->product_id)->first();
            $cost_item = ceil($this->parser->parser_coefficient * $product_parser->price);
            return new ParserItem(
                $product_parser->product,
                $product_parser,
                (int)$storage->quantity,
                (int)$cost_item,
                $this->quantityHTML($product_parser->quantity)
            );

        }, $this->parserStorageQuery()->getModels());
    }

    private function merge_id_ui()
    {
        /** @var ParserStorage[] $items */
        //dd($this->user_id);
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
            return ParserStorage::where('user_ui', $this->user_ui)->orderByDesc('created_at');
        } else {
            return ParserStorage::where('user_id', $this->user_id)->orderByDesc('created_at');
        }
    }

    private function quantityHTML(array $quantity): string
    {
        $result = '<div class="row">';
        foreach (ParserService::STORES as $store => $name) {
            $_count = $quantity[$store] ?? 0;
            $_class = ($_count > 1) ? 'ikea-green' : ( ($_count == 1) ? 'ikea-yellow' : 'ikea-red');
            $result .= '<div class="col-xl-3 col-lg-4 col-6">';
            $result .= '<span class="w39-point ' . $_class  . '">' . $name . '</span>  ';
            $result .= '</div>';
        }

        return $result . '</div>';
    }

}
