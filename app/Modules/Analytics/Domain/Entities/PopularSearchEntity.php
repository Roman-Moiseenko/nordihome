<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use DateTimeImmutable;

/**
 * PopularSearch — агрегат популярных поисковых запросов за период.
 *
 * Ключ агрегации: query_normalized + period_date + period_type.
 */
final class PopularSearchEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $queryNormalized {
        get => $this->queryNormalized;
        set => $this->queryNormalized = $value;
    }

    public string $querySample {
        get => $this->querySample;
        set => $this->querySample = $value;
    }

    public int $searchesCount = 0 {
        get => $this->searchesCount;
        set => $this->searchesCount = $value;
    }

    public int $uniqueVisitorsCount = 0 {
        get => $this->uniqueVisitorsCount;
        set => $this->uniqueVisitorsCount = $value;
    }

    public int $clicksCount = 0 {
        get => $this->clicksCount;
        set => $this->clicksCount = $value;
    }

    public string $periodDate {
        get => $this->periodDate;
        set => $this->periodDate = $value;
    }

    public string $periodType {
        get => $this->periodType;
        set => $this->periodType = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(
        string $queryNormalized,
        string $querySample,
        string $periodDate,
        string $periodType,
    ) {
        $this->queryNormalized = $queryNormalized;
        $this->querySample = $querySample;
        $this->periodDate = $periodDate;
        $this->periodType = $periodType;
    }

    public function increment(int $visitorsDelta, int $clicksDelta): void
    {
        $this->searchesCount++;
        $this->uniqueVisitorsCount += $visitorsDelta;
        $this->clicksCount += $clicksDelta;
    }
}
