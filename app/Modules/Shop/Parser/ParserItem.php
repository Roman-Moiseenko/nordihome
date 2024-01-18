<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Modules\Product\Entity\Product;

class ParserItem
{
    public int $quantity;
    public Product $product;
    public float $base_cost;

    public function __construct(int $quantity, Product $product, float $base_cost = 0)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->base_cost = $base_cost;
    }

    public function setCost(float $base_cost)
    {
        $this->base_cost = $base_cost;
    }

}
