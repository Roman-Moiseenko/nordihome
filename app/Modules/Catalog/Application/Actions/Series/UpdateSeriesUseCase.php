<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Catalog\Application\DTOs\Series\SeriesUpdateData;
use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class UpdateSeriesUseCase
{
    public function __construct(
        private SeriesRepositoryInterface $seriesRepository,
    )
    {
    }

    public function execute(int $id, SeriesUpdateData $dto, UserPermission $userPermission): SeriesEntity
    {
        if (!$userPermission->can('catalog.product.edit')) {
            throw new AccessDeniedException();
        }

        $series = $this->seriesRepository->getById($id);

        $series->name = trim($dto->name);
        $series->nameRu = $dto->nameRu ?? '';

        return $this->seriesRepository->save($series);
    }
}
