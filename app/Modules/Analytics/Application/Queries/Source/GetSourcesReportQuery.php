<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Queries\Source;

use App\Modules\Analytics\Domain\Entities\SourceDailyEntity;
use App\Modules\Analytics\Domain\Interfaces\SourceDailyRepositoryInterface;
use DateTimeImmutable;

/**
 * GetSourcesReport — отчёт по источникам трафика за период.
 */
final class GetSourcesReportQuery
{
    public function __construct(
        private readonly SourceDailyRepositoryInterface $sourceDaily,
    ) {}

    /**
     * @return SourceDailyEntity[]
     */
    public function execute(DateTimeImmutable $from, DateTimeImmutable $to, ?string $source = null): array
    {
        return $this->sourceDaily->getReport($from, $to, $source);
    }
}
