<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Report;

use App\Modules\Analytics\Application\DTOs\Analytics\AnalyticsFilterData;
use App\Modules\Analytics\Domain\Interfaces\AnalyticsReportRepositoryInterface;

/**
 * GetSourcesReportQuery — отчёт по источникам трафика.
 */
readonly class GetSourcesReportQuery
{
    public function __construct(
        private AnalyticsReportRepositoryInterface $reports,
    ) {
    }

    public function execute(AnalyticsFilterData $filter): array
    {
        return $this->reports->sources($filter->getFrom(), $filter->getTo());
    }
}
