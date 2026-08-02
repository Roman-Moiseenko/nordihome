<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Catalog\Application\Interfaces\ProductRepositoryInterface;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;

readonly class AttachProductToParserUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $repositoryParserProduct,
    )
    {
    }

    public function execute(int $parserId, int $productId): void
    {

            $parser = $this->repositoryParserProduct->getById($parserId);
            $parser->productId = $productId;
            $this->repositoryParserProduct->save($parser);

    }
}
