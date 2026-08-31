<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Catalog\Domain\Entities\ProductEntity;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;

readonly class FindAndAttachToProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface       $repositoryProduct,
        private ParserProductRepositoryInterface $repositoryParserProduct,
    )
    {
    }

    public function execute(int $parser_id, string $code):? ProductEntity
    {
        if (!is_null($product = $this->repositoryProduct->getByCode($code))) {
            $parser = $this->repositoryParserProduct->getById($parser_id);
            $parser->productId = $product->id;


            $this->repositoryParserProduct->save($parser);
            return $product;
        }
        return null;
    }
}
