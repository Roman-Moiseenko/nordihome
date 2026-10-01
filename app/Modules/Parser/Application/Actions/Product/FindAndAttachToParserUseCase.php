<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;

readonly class FindAndAttachToParserUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $repositoryParserProduct,
    )
    {
    }

    public function execute(int $productId, string $code):? ParserProductEntity
    {
        if (!is_null($parser = $this->repositoryParserProduct->getByCode($code))) {
            $parser->productId = $productId;
            return $this->repositoryParserProduct->save($parser);
        }
        return null;
    }
}
