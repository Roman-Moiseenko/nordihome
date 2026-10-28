<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Report;

use App\Modules\Analytics\Application\DTOs\Analytics\AnalyticsFilterData;
use App\Modules\Analytics\Domain\Interfaces\AnalyticsReportRepositoryInterface;

/**
 * GetActionsReportQuery — отчёт по действиям клиентов.
 */
readonly class GetActionsReportQuery
{
    public function __construct(
        private AnalyticsReportRepositoryInterface $reports,
    ) {
    }

    public function execute(AnalyticsFilterData $filter): array
    {
        return $this->reports->actions($filter->getFrom(), $filter->getTo());
    }
}
