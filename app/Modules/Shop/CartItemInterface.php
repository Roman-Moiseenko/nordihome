<?php
declare(strict_types=1);

namespace App\Modules\Shop;

use App\Modules\Order\Entity\Reserve;
use App\Modules\Product\Entity\Product;

//TODO проработать все поля
interface CartItemInterface
{
    public function getProduct(): Product;
    public function getQuantity(): int;

    public function getBaseCost(): float;
    public function getSellCost(): float;
    public function getDiscount():? int;

    public function getDiscountType(): string;
    public function getOptions(): array;
    public function getReserve():? Reserve;
    public function getCheck(): bool;
    public function getPreorder(): bool;


    public function setSellCost(float $discount_cost): void; //discount_cost
    public function setDiscountName(string $discount_name): void; //discount_name
    public function setDiscount(int $discount_id): void; //discount_id
    public function setDiscountType(string $discount_type): void; //discount_type

}
