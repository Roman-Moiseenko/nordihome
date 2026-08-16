<?php
declare(strict_types=1);

namespace App\Modules\Cart\Domain\Entities;

use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Shop\CartItemInterface;

class CartItem implements CartItemInterface
{
    public Product $product;
    public int $productId;
    public int $id;
    public float $quantity;
    public float $base_cost; //Базовая цена  - используется для удобства = $product->getLastPrice()
    public float $discount_cost; //Цена со скидкой
    public string $discount_name= ''; //Название акции
    public ?int $discount_id = null;
    public string $discount_type; //Класс скидка Promotion или Bonus
//    public bool $pre_order;
    public bool $check;
    public bool $is_parser = false;

    public static function create(int $productId, float $quantity, bool $is_parser): self
    {
        $item = new static();

        $item->productId = $productId;
        $item->quantity = $quantity;
        $item->is_parser = $is_parser;
        //$item->base_cost = $product->getPrice();
        $item->check = true;

        return $item;
    }

    public static function load(int $id, Product $product, float $quantity, $is_parser, bool $check): self
    {
        $item = new static();
        $item->id = $id;
        $item->productId = $product->id;
        $item->product = $product;
        $item->quantity = $quantity;
        $item->is_parser = $is_parser;
        $item->check = $check;
        $item->base_cost = $is_parser ? $product->parser->price_base : $product->getPrice();
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

    public function getQuantity(): float
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

    public function availability(): float
    {
        return $this->product->getQuantitySell();
    }

    public function withQuantity(float $quantity): self
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
        if ($this->is_parser) return $this->base_cost; //Для парсера нет скидок
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

    public function isParser(): bool
    {
        return $this->is_parser;
    }
}

