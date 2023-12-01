<?php
declare(strict_types=1);

namespace App\Modules\Order\Entity;

use App\Modules\Product\Entity\Product;

interface ItemInterface
{
    public function getProduct(): Product;
    public function baseCost(): float;
    public function sellCost(): float;
    public function discount(): string;

    public function setSellCost(float $cost): void;
    public function setDiscount(string $discount): void;

}
