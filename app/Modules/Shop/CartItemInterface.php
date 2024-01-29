<?php
declare(strict_types=1);

namespace App\Modules\Shop;

use App\Modules\Product\Entity\Product;

interface CartItemInterface
{
    public function getProduct(): Product;
    public function getQuantity(): int;
}
