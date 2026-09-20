<?php

declare(strict_types=1);

namespace App\Modules\Output\Application\Queries\Feed;

use App\Modules\Output\Application\DTOs\Feed\FeedCategoryData;
use App\Modules\Output\Application\DTOs\Feed\FeedExportData;
use App\Modules\Output\Application\DTOs\Feed\FeedInfoData;
use App\Modules\Output\Application\DTOs\Feed\FeedProductData;
use App\Modules\Output\Domain\Entities\FeedEntity;
use App\Modules\Output\Infrastructure\Persistence\Query\FeedExportQueryRepository;

readonly class GetFeedForYandexQuery
{
    public function __construct(
        private FeedExportQueryRepository $repository,
    ) {}

    public function execute(FeedEntity $feed): FeedExportData
    {
        $export = $this->repository->getCachedExport($feed);

        return new FeedExportData(
            info: FeedInfoData::from($export['info']),
            products: array_map(static fn (array $product) => FeedProductData::from($product), $export['products']),
            categories: array_map(static fn (array $category) => FeedCategoryData::from($category), $export['categories']),
        );
    }
}
