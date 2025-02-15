<?php

namespace App\Modules\Parser\Service;

use App\Modules\Parser\Entity\ProductParser;

class ProductParserService
{

    public function parserProduct(ProductParser $product): float
    {
        $brand = $product->product->brand;
        $parser_class = $brand->parser_class;
        /** @var ParserAbstract $parser */
        $parser = app()->make($parser_class);

        return $parser->parserCost($product);
    }
}
