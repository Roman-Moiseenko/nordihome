<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\CartItemInterface;

class CartItem implements CartItemInterface
{
    public Product $product;
    public int $id;
    public int $quantity;
    public float $base_cost; //Базовая цена  - используется для удобства = $product->getLastPrice()
    public float $discount_cost; //Цена со скидкой
    public string $discount_name; //Название акции
    public int $discount_id;
    public string $discount_type; //Класс скидка Promotion или Bonus
    public array $options;
//    public bool $pre_order;
    public bool $check;

    public static function create(Product $product, int $quantity, array $options): self
    {
        $item = new static();

        $item->product = $product;
        $item->quantity = $quantity;
        $item->options = $options;
        $item->base_cost = $product->getLastPrice();
        $item->check = true;
        $item->discount_name = '';
        $item->discount_cost = 0;
        return $item;
    }

    public static function load(int $id, Product $product, $quantity, $options, bool $check): self
    {
        $item = new static();
        $item->id = $id;
        $item->product = $product;
        $item->quantity = $quantity;
        $item->options = $options;
        $item->check = $check;
        $item->base_cost = $product->getLastPrice();
        $item->discount_name = '';
        $item->discount_cost = 0;
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
        return $this->product->getCountSell();
    }

    public function withQuantity(int $quantity): self
    {
        $item = clone $this;
        $item->quantity = $quantity;
        return $item;
    }

    public function withNotReserve(): self
    {
        return clone $this;
    }

    public function getBaseCost(): float
    {
        return $this->base_cost;
    }

    public function getSellCost(): float
    {
        return ($this->discount_cost == 0) ? $this->base_cost : $this->discount_cost;
    }

    public function getDiscount(): ?int
    {
        return $this->discount_id ?? null;
    }

    public function getDiscountType(): string
    {
        return $this->discount_type ?? '';
    }

    public function getOptions(): array
    {
        return $this->options;
    }


    public function getCheck(): bool
    {
        return $this->check;
    }

    public function setSellCost(float $discount_cost): void
    {
        $this->discount_cost = $discount_cost;
    }

    public function setDiscountName(string $discount_name): void
    {
        $this->discount_name = $discount_name;
    }

    public function setDiscount(int $discount_id): void
    {
        $this->discount_id = $discount_id;
    }

    public function setDiscountType(string $discount_type): void
    {
        $this->discount_type = $discount_type;
    }

    public function getPreorder(): bool
    {
        return false;
    }
}

