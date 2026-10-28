<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Report;

use App\Modules\Analytics\Application\DTOs\Analytics\AnalyticsFilterData;
use App\Modules\Analytics\Domain\Interfaces\AnalyticsReportRepositoryInterface;

/**
 * GetPagesReportQuery — отчёт по просмотрам страниц.
 */
readonly class GetPagesReportQuery
{
    public function __construct(
        private AnalyticsReportRepositoryInterface $reports,
    ) {
    }

    public function execute(AnalyticsFilterData $filter): array
    {
        return $this->reports->pages($filter->getFrom(), $filter->getTo());
    }
}
