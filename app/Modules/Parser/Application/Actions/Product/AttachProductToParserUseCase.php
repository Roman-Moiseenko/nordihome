<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;

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
