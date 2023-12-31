<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;

class CartItem
{
    public Product $product;
    public ?Reserve $reserve;
    public int $id;
    public int $quantity;
    public float $base_cost = -1;
    public float $discount_cost = 0;
    public string $discount_name = '';
    public int $discount_id;
    public string $discount_type;
    public array $options;
    public bool $pre_order;
    public bool $check;

    public function __construct()
    {
        $this->pre_order = (new Options())->shop->pre_order;
    }

    public static function create(Product $product, int $quantity, array $options): self
    {
        $item = new static();

        if (!$item->pre_order && $product->count_for_sell < $quantity) {
            throw new \DomainException('Превышение остатка');
        }
        $item->product = $product;
        $item->quantity = $quantity;
        $item->options = $options;
        $item->base_cost = $product->lastPrice->value;
        $item->check = true;
        return $item;
    }

    public static function load(int $id, Product $product, $quantity, $options, bool $check, $reserve = null): self
    {
        $item = new static();
        $item->id = $id;
        $item->product = $product;
        $item->quantity = $quantity;
        $item->options = $options;
        $item->check = $check;
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

    public function check(): void
    {
        $this->check = !$this->check;
    }

    public function preorder(): bool
    {
        return $this->quantity > $this->availability();
    }

    public function availability(): int
    {
        if ($this->reserve == null) {
            return $this->product->count_for_sell;
        } else {
            return $this->reserve->quantity;
        }
    }

    public function withQuantity(int $quantity): self
    {
        $item = clone $this;
        $item->quantity = $quantity;
        return $item;
    }

    public function withNotReserve(): self
    {
        $item = clone $this;
        $item->reserve = null;
        return $item;
    }
}

