<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Modules\Product\Entity\Product;
use App\Modules\Shop\CartItemInterface;

class ParserItem implements CartItemInterface
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

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
