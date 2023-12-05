<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity\Options;

class Shop
{
    public bool $pre_order;
    public bool $only_offline;
    public bool $show_finished;
    public array $delivery;
    public int $paginate;
    public int $reserve_cart;
    public int $cookie_timeout;
    public string $cookie_key;

    public static function createFromArray(mixed $shop): static
    {
        $_shop = new static();
        $_shop->pre_order = $shop['pre_order'];
        $_shop->only_offline = $shop['only-offline'];
        $_shop->delivery = $shop['delivery'];
        $_shop->show_finished = $shop['show-finished'];
        $_shop->paginate = $shop['paginate'];
        $_shop->reserve_cart = $shop['reserve_cart'];
        $_shop->cookie_key = $shop['cookie']['key'];
        $_shop->cookie_timeout = $shop['cookie']['timeout'];
        return $_shop;
    }
}
