<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use DateTimeImmutable;

/**
 * Search — поисковый запрос посетителя.
 *
 * Событийная (append-only) запись: фиксирует исходную и нормализованную строку
 * запроса, количество результатов и (опционально) клик по результату.
 */
final class SearchEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $visitorId {
        get => $this->visitorId;
        set => $this->visitorId = $value;
    }

    public ?int $sessionId = null {
        get => $this->sessionId;
        set => $this->sessionId = $value;
    }

    public ?int $pageViewId = null {
        get => $this->pageViewId;
        set => $this->pageViewId = $value;
    }

    public string $query {
        get => $this->query;
        set => $this->query = $value;
    }

    public string $queryNormalized {
        get => $this->queryNormalized;
        set => $this->queryNormalized = $value;
    }

    public int $resultsCount = 0 {
        get => $this->resultsCount;
        set => $this->resultsCount = $value;
    }

    public ?int $clickedResultId = null {
        get => $this->clickedResultId;
        set => $this->clickedResultId = $value;
    }

    public ?string $clickedResultType = null {
        get => $this->clickedResultType;
        set => $this->clickedResultType = $value;
    }

    public ?int $clickedPosition = null {
        get => $this->clickedPosition;
        set => $this->clickedPosition = $value;
    }

    public DateTimeImmutable $searchedAt {
        get => $this->searchedAt;
        set => $this->searchedAt = $value;
    }

    public bool $isBot = false {
        get => $this->isBot;
        set => $this->isBot = $value;
    }

    public ?DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    public function __construct(
        int $visitorId,
        string $query,
        string $queryNormalized,
        DateTimeImmutable $searchedAt,
    ) {
        $this->visitorId = $visitorId;
        $this->query = $query;
        $this->queryNormalized = $queryNormalized;
        $this->searchedAt = $searchedAt;
    }

    /**
     * Фиксирует клик по результату выдачи.
     */
    public function trackClick(int $resultId, ?string $resultType = null, ?int $position = null): void
    {
        $this->clickedResultId = $resultId;
        $this->clickedResultType = $resultType;
        $this->clickedPosition = $position;
    }

    public function markAsBot(): void
    {
        $this->isBot = true;
    }
}
