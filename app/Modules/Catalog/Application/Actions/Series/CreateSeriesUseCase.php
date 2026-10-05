<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Catalog\Application\DTOs\Series\SeriesCreateData;
use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class CreateSeriesUseCase
{
    public function __construct(
        private SeriesRepositoryInterface $seriesRepository,
    )
    {
    }

    public function execute(SeriesCreateData $dto, UserPermission $userPermission): SeriesEntity
    {
        if (!$userPermission->can('catalog.product.create')) {
            throw new AccessDeniedException();
        }

        $series = $this->seriesRepository->getByName(trim($dto->name));

        if ($series !== null) {
            return $series;
        }

        $series = new SeriesEntity(
            name: trim($dto->name),
            nameRu: $dto->nameRu ?? '',
        );

        return $this->seriesRepository->save($series);
    }
}
