<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;

readonly class AttachProductToParserUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $repositoryParserProduct,
    )
    {
    }

    public function execute(int $parserId, int $productId): ParserProductEntity
    {
        $parser = $this->repositoryParserProduct->getById($parserId);
        $parser->productId = $productId;
        return $this->repositoryParserProduct->save($parser);
    }
}
