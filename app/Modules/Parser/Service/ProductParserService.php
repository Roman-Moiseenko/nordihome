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

    public function parserProducts(mixed $input)
    {
//        foreach ()
    }

    public function available(ProductParser $product): string
    {
        $message = $product->availability ? 'Товар больше недоступен' : 'Товар доступен для заказа';
        $product->availability = !$product->availability;
        $product->save();
        return $message;
    }

    public function fragile(ProductParser $product): string
    {
        $message = $product->fragile ? 'Товар не хрупкий' : 'Товар хрупкий';
        $product->fragile = !$product->fragile;
        $product->save();
        return $message;
    }

    public function sanctioned(ProductParser $product): string
    {
        $message = $product->sanctioned ? 'Товар не санкционный' : 'Товар санкционный';
        $product->sanctioned = !$product->sanctioned;
        $product->save();
        return $message;
    }
}
