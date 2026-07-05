<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;

class FindAndAttachToProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $repositoryProduct,
        private readonly ParserProductRepositoryInterface $repositoryParserProduct,
    )
    {
    }

    public function execute(int $parser_id, string $code): void
    {
        if (!is_null($product = $this->repositoryProduct->getByCode($code))) {
            $parser = $this->repositoryParserProduct->getById($parser_id);
            $parser->productId = $product->id;
            $this->repositoryParserProduct->save($parser);
        }
    }
}
