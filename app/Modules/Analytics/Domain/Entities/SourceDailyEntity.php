<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use DateTimeImmutable;

/**
 * SourceDaily — дневной агрегат по источникам трафика.
 *
 * Ключ агрегации: date + source (+ utm-разрезы).
 */
final class SourceDailyEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $date {
        get => $this->date;
        set => $this->date = $value;
    }

    public string $source {
        get => $this->source;
        set => $this->source = $value;
    }

    public ?string $utmSource = null {
        get => $this->utmSource;
        set => $this->utmSource = $value;
    }

    public ?string $utmMedium = null {
        get => $this->utmMedium;
        set => $this->utmMedium = $value;
    }

    public ?string $utmCampaign = null {
        get => $this->utmCampaign;
        set => $this->utmCampaign = $value;
    }

    public int $sessionsCount = 0 {
        get => $this->sessionsCount;
        set => $this->sessionsCount = $value;
    }

    public int $uniqueVisitorsCount = 0 {
        get => $this->uniqueVisitorsCount;
        set => $this->uniqueVisitorsCount = $value;
    }

    public int $newVisitorsCount = 0 {
        get => $this->newVisitorsCount;
        set => $this->newVisitorsCount = $value;
    }

    public int $bounceCount = 0 {
        get => $this->bounceCount;
        set => $this->bounceCount = $value;
    }

    public ?int $avgDuration = null {
        get => $this->avgDuration;
        set => $this->avgDuration = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(string $date, string $source)
    {
        $this->date = $date;
        $this->source = $source;
    }

    /**
     * Учитывает одну сессию в агрегате.
     */
    public function recordSession(bool $uniqueVisitor, bool $newVisitor, bool $bounce, ?int $duration = null): void
    {
        $this->sessionsCount++;
        if ($uniqueVisitor) {
            $this->uniqueVisitorsCount++;
        }
        if ($newVisitor) {
            $this->newVisitorsCount++;
        }
        if ($bounce) {
            $this->bounceCount++;
        }
        if ($duration !== null) {
            $this->avgDuration = $this->avgDuration === null
                ? $duration
                : (int)round(($this->avgDuration * ($this->sessionsCount - 1) + $duration) / $this->sessionsCount);
        }
    }
}
