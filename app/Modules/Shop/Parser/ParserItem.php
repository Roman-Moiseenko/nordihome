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
    public string $storages = '';

    public function __construct(Product $product, ProductParser $parser, int $quantity, int $cost, string $storages)
    {
        $this->product = $product;
        $this->parser = $parser;
        $this->quantity = $quantity;
        $this->cost = $cost;
        $this->storages = $storages;
    }
}
