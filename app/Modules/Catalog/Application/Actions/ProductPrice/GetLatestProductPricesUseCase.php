<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\ProductPrice;

use App\Modules\Catalog\Domain\Interfaces\ProductPriceRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class GetLatestProductPricesUseCase
{
    public function __construct(
        private ProductPriceRepositoryInterface $priceRepository,
    )
    {
    }

    /**
     * @return array<string, float>
     */
    public function execute(int $productId, UserPermission $userPermission): array
    {
        if (!$userPermission->can('catalog.product.price.view')) {
            throw new AccessDeniedException();
        }

        return $this->priceRepository->findCurrentPrices($productId);
    }
}
