<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Page;

use App\Modules\Analytics\Domain\Interfaces\PageDailyRepositoryInterface;
use App\Modules\Analytics\Domain\ReadModels\PageDailyData;
use DateTimeImmutable;

/**
 * GetTopPages — топ страниц по типу за день (по дневному агрегату).
 */
final class GetTopPagesQuery
{
    public function __construct(
        private readonly PageDailyRepositoryInterface $pageDaily,
    ) {}

    /**
     * @return PageDailyData[]
     */
    public function execute(
        DateTimeImmutable $date,
        string $pageType,
        int $limit = 20,
        string $sortBy = 'views_count',
    ): array {
        return $this->pageDaily->getTopByType($date, $pageType, $limit, $sortBy);
    }
}
