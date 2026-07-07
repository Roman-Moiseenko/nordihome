<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Actions\Product;

use App\Modules\Parser\Application\Interfaces\ParserProductRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ToggleProductAvailabilityUseCase
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
        $product->availability = !$product->availability;

        $this->productRepository->save($product);

        return $product->availability
            ? 'Товар доступен для заказа'
            : 'Товар больше недоступен';
    }
}
