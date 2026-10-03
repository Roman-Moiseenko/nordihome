<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Parser\Domain\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class NewSellPriceParserProductUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id, float $priceSell): ParserProductEntity
    {
        $product = $this->productRepository->getById($id);
        $product->priceSell = $priceSell;

        return $this->productRepository->save($product);

    }
}
