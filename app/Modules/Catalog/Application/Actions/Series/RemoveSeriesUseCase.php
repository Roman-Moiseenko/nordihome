<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class RemoveSeriesUseCase
{
    public function __construct(
        private SeriesRepositoryInterface $seriesRepository,
    )
    {
    }

    public function execute(int $id, UserPermission $userPermission): void
    {
        if (!$userPermission->can('catalog.product.delete')) {
            throw new AccessDeniedException();
        }

        $this->seriesRepository->detachAllProducts($id);
        $this->seriesRepository->delete($id);
    }
}
