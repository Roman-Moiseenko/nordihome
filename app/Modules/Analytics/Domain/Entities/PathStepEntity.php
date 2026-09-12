<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use DateTimeImmutable;

/**
 * PathStep — шаг пути клиента в рамках сессии (для быстрого анализа воронок).
 *
 * Фиксирует порядковый номер шага, страницу, время на ней и действие,
 * совершённое на этом шаге (если было).
 */
final class PathStepEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $sessionId {
        get => $this->sessionId;
        set => $this->sessionId = $value;
    }

    public int $visitorId {
        get => $this->visitorId;
        set => $this->visitorId = $value;
    }

    public int $stepNumber {
        get => $this->stepNumber;
        set => $this->stepNumber = $value;
    }

    public string $pageType {
        get => $this->pageType;
        set => $this->pageType = $value;
    }

    public ?int $entityId = null {
        get => $this->entityId;
        set => $this->entityId = $value;
    }

    public string $url {
        get => $this->url;
        set => $this->url = $value;
    }

    public ?int $duration = null {
        get => $this->duration;
        set => $this->duration = $value;
    }

    public ?string $actionType = null {
        get => $this->actionType;
        set => $this->actionType = $value;
    }

    public DateTimeImmutable $occurredAt {
        get => $this->occurredAt;
        set => $this->occurredAt = $value;
    }

    public function __construct(
        int $sessionId,
        int $visitorId,
        int $stepNumber,
        string $pageType,
        string $url,
        DateTimeImmutable $occurredAt,
    ) {
        $this->sessionId = $sessionId;
        $this->visitorId = $visitorId;
        $this->stepNumber = $stepNumber;
        $this->pageType = $pageType;
        $this->url = $url;
        $this->occurredAt = $occurredAt;
    }
}
