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

}
