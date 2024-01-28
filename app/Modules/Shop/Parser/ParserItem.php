<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Modules\Product\Entity\Product;

class ParserItem
{
    public Product $product;
    public ProductParser $parser;
    public int $quantity;
    public int $cost;

    public function __construct(Product $product, ProductParser $parser, int $quantity, int $cost)
    {
        $this->product = $product;
        $this->parser = $parser;
        $this->quantity = $quantity;
        $this->cost = $cost;
    }

}
