<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Actions\ProductPrice;

use App\Modules\Accounting\Infrastructure\Interfaces\PriceRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class GetLatestPricesQuery
{
    public function __construct(
        private PriceRepositoryInterface $priceRepository,
    )
    {
    }

    /**
     * @return array<string, float>
     */
    public function execute(int $productId, UserPermission $userPermission): array
    {
        if (!$userPermission->can('accounting.price.view')) {
            throw new AccessDeniedException();
        }

        return $this->priceRepository->findCurrentPrices($productId);
    }
}
