<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Actions\Series;

use App\Modules\Base\Service\TranslateService;
use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use App\Modules\Catalog\Domain\Interfaces\SeriesRepositoryInterface;

readonly class FindOrCreateSeriesUseCase
{
    public function __construct(
        private SeriesRepositoryInterface $seriesRepository,
        private TranslateService $translateService,
    ) {}

    public function execute(string $name, ?string $nameRu = null): SeriesEntity
    {
        $series = $this->seriesRepository->getByName($name);

        if ($series === null) {
            $series = new SeriesEntity(
                name: $name,
                nameRu: $nameRu ?: $this->translateService->translate($name),
            );

            $series = $this->seriesRepository->save($series);
        }

        return $series;
    }
}
