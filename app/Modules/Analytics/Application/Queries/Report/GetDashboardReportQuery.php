<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Report;

use App\Modules\Analytics\Application\DTOs\Analytics\AnalyticsFilterData;
use App\Modules\Analytics\Domain\Interfaces\AnalyticsReportRepositoryInterface;

/**
 * GetDashboardReportQuery — сводный отчёт для дашборда аналитики.
 */
readonly class GetDashboardReportQuery
{
    public function __construct(
        private AnalyticsReportRepositoryInterface $reports,
    ) {
    }

    public function execute(AnalyticsFilterData $filter): array
    {
        return $this->reports->dashboard($filter->getFrom(), $filter->getTo());
    }
}
