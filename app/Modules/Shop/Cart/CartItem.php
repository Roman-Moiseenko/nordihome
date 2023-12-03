<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\Reserve;

class CartItem
{
    public Product $product;
    public ?Reserve $reserve;
    public int $id;
    public int $quantity;
    public float $base_cost = -1;
    public float $discount_cost;
    public string $discount_name = '';
    public int $discount_id;
    public array $options;
    public bool $preorder;

    public function __construct()
    {
        $this->preorder = (new Options())->shop->preorder;
    }

    public static function create(Product $product, int $quantity, array $options): self
    {
        $item = new static();

        if (!$item->preorder && $product->count_for_sell < $quantity) {
            throw new \DomainException('Превышение остатка');
        }
        $item->product = $product;
        $item->quantity = $quantity;
        $item->options = $options;
        $item->base_cost = $product->lastPrice->value;
        return $item;
    }

    public static function load(int $id, Product $product, $quantity, $options, $reserve = null): self
    {
        $item = new static();
        $item->id = $id;
        $item->product = $product;
        $item->quantity = $quantity;
        $item->options = $options;
        $item->reserve = $reserve;

        $item->base_cost = $product->lastPrice->value;
        return $item;
    }

    public function isProduct(int $product_id): bool
    {
        return $this->product->id == $product_id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

}

