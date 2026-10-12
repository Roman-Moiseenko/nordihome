<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\SizeGrid;

use App\Modules\Catalog\Application\DTOs\SizeGrid\SizeGridIndexData;
use App\Modules\Catalog\Domain\Entities\SizeGridEntity;
use App\Modules\Catalog\Domain\Interfaces\SizeGridRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class IndexSizeGridQuery
{
    public function __construct(
        private SizeGridRepositoryInterface $sizeGridRepository,
    ) {
    }

    /**
     * @return SizeGridIndexData[]
     */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('catalog.product.view')) {
            throw new AccessDeniedException();
        }

        return array_map(
            fn(SizeGridEntity $grid) => SizeGridIndexData::fromEntity($grid),
            $this->sizeGridRepository->getAll(),
        );
    }
}
