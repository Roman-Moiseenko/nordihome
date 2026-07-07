<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ToggleProductSanctionedUseCase
{
    public function __construct(
        private ParserProductRepositoryInterface $productRepository,
    ) {}

    public function execute(int $id, UserPermission $userPermission): string
    {
        if (!$userPermission->can('parser.product.edit')) {
            throw new \DomainException('Доступ запрещён');
        }

        $product = $this->productRepository->getById($id);
        $product->sanctioned = !$product->sanctioned;

        $this->productRepository->save($product);

        return $product->sanctioned
            ? 'Товар санкционный'
            : 'Товар не санкционный';
    }
}
