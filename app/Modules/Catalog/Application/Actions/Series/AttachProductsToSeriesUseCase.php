<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class AttachProductsToSeriesUseCase
{
    public function __construct(
        private SeriesRepositoryInterface $seriesRepository,
    )
    {
    }

    /**
     * @param int[] $productIds
     */
    public function execute(int $seriesId, array $productIds, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $this->seriesRepository->attachProducts($seriesId, $productIds);
    }
}
