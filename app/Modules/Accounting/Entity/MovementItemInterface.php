<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Product\Entity\Product;

interface MovementItemInterface
{
    public function getProduct(): Product;
    public function getQuantity(): int;
}
