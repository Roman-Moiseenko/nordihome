<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;

readonly class FindAndAttachToParserUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $repositoryParserProduct,
    )
    {
    }

    public function execute(int $productId, string $code): void
    {
        if (!is_null($parser = $this->repositoryParserProduct->getByCode($code))) {
            $parser->productId = $productId;
            $this->repositoryParserProduct->save($parser);
        }
    }
}
