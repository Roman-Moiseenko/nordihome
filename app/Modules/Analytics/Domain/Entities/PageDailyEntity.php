<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use DateTimeImmutable;

/**
 * PageDaily — дневной агрегат по страницам.
 *
 * Ключ агрегации: date + page_type + entity_id.
 */
final class PageDailyEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public string $date {
        get => $this->date;
        set => $this->date = $value;
    }

    public string $pageType {
        get => $this->pageType;
        set => $this->pageType = $value;
    }

    public ?int $entityId = null {
        get => $this->entityId;
        set => $this->entityId = $value;
    }

    public int $viewsCount = 0 {
        get => $this->viewsCount;
        set => $this->viewsCount = $value;
    }

    public int $uniqueVisitorsCount = 0 {
        get => $this->uniqueVisitorsCount;
        set => $this->uniqueVisitorsCount = $value;
    }

    public ?int $avgDuration = null {
        get => $this->avgDuration;
        set => $this->avgDuration = $value;
    }

    public int $bounceCount = 0 {
        get => $this->bounceCount;
        set => $this->bounceCount = $value;
    }

    public ?DateTimeImmutable $updatedAt = null {
        get => $this->updatedAt;
        set => $this->updatedAt = $value;
    }

    public function __construct(string $date, string $pageType)
    {
        $this->date = $date;
        $this->pageType = $pageType;
    }

    /**
     * Учитывает один просмотр страницы.
     */
    public function recordView(bool $uniqueVisitor, ?int $duration = null): void
    {
        $this->viewsCount++;
        if ($uniqueVisitor) {
            $this->uniqueVisitorsCount++;
        }
        if ($duration !== null) {
            $this->avgDuration = $this->avgDuration === null
                ? $duration
                : (int)round(($this->avgDuration * ($this->viewsCount - 1) + $duration) / $this->viewsCount);
        }
    }

    public function recordBounce(): void
    {
        $this->bounceCount++;
    }
}
