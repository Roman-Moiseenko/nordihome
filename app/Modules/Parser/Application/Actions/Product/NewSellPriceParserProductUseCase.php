<?php

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Parser\Domain\Entities\ParserProductEntity;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class NewSellPriceParserProductUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id, float $priceSell, UserPermission $userPermission): ParserProductEntity
    {
        if (!$userPermission->can('parser.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $product = $this->productRepository->getById($id);
        $product->priceSell = $priceSell;

        return $this->productRepository->save($product);

    }
}
