<?php

namespace App\Modules\Parser\Service;

use App\Modules\Product\Entity\Product;

abstract class ParserAbstract
{
    abstract public function findProduct(string $search): Product;

    abstract public function remainsProduct(string $code): float;

    abstract public function costProduct(string $code): float;

    abstract public function availablePrice(string $code): bool;

}
