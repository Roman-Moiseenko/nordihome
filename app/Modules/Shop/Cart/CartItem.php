<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

use App\Modules\Admin\Entity\Options;
use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;
use App\Modules\Shop\CartItemInterface;

class CartItem implements CartItemInterface
{
    public Product $product;
    public ?Reserve $reserve;
    public int $id;
    public int $quantity;
    public float $base_cost = -1; //Базовая цена  - используется для удобства = $product->getLastPrice()
    public float $discount_cost = 0; //Цена со скидкой
    public string $discount_name = ''; //Название акции
    public int $discount_id;
    public string $discount_type; //Класс скидка Promotion или Bonus
    public array $options;
    public bool $pre_order;
    public bool $check;

    public function __construct()
    {
        $this->pre_order = (new Options())->shop->pre_order;
        $this->reserve = null;
    }

    public static function create(Product $product, int $quantity, array $options, bool $check_quantity = true): self
    {
        $item = new static();

        if (!$item->pre_order && $product->count_for_sell < $quantity && $check_quantity == true) {
            throw new \DomainException('Превышение остатка');
        }
        $item->product = $product;
        $item->quantity = $quantity;
        $item->options = $options;
        $item->base_cost = $product->getLastPrice();
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
        $item->base_cost = $product->getLastPrice();
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

    public function getReserve(): ?Reserve
    {
        return $this->reserve;
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

