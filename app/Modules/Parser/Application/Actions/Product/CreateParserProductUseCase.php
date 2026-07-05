<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Application\DTOs\Product\ParserProductCreateData;
use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class CreateParserProductUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $productRepository,
    )
    {
    }

    public function execute(ParserProductCreateData $dto): ParserProductEntity
    {

        $product = new ParserProductEntity(
            name: $dto->name,
            code: $dto->code,
        );
        $product->short = $dto->short;

        return $this->productRepository->save($product);
    }


}
