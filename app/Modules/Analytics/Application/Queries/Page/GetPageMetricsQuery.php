<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Page;

use App\Modules\Analytics\Domain\Interfaces\PageDailyRepositoryInterface;
use App\Modules\Analytics\Domain\ReadModels\PageMetricsData;
use DateTimeImmutable;

/**
 * GetPageMetrics — сводные (средние) метрики по типу страницы за период.
 */
final class GetPageMetricsQuery
{
    public function __construct(
        private readonly PageDailyRepositoryInterface $pageDaily,
    ) {}

    public function execute(
        string $pageType,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): ?PageMetricsData {
        return $this->pageDaily->getAvgMetrics($pageType, $from, $to);
    }
}
