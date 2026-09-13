<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use DateTimeImmutable;

/**
 * Action — действие клиента на сайте (append-only).
 *
 * Фиксирует тип действия (add_to_cart, click_buy, form_submit, ...), связанную
 * сущность и произвольные дополнительные данные (payload).
 */
final class ActionEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $visitorId {
        get => $this->visitorId;
        set => $this->visitorId = $value;
    }

    public int $sessionId {
        get => $this->sessionId;
        set => $this->sessionId = $value;
    }

    public ?int $pageViewId = null {
        get => $this->pageViewId;
        set => $this->pageViewId = $value;
    }

    public ActionType $actionType {
        get => $this->actionType;
        set => $this->actionType = $value;
    }

    public ?EntityType $entityType = null {
        get => $this->entityType;
        set => $this->entityType = $value;
    }

    public ?int $entityId = null {
        get => $this->entityId;
        set => $this->entityId = $value;
    }

    /** @var array<string, mixed>|null */
    public ?array $payload = null {
        get => $this->payload;
        set => $this->payload = $value;
    }

    public DateTimeImmutable $occurredAt {
        get => $this->occurredAt;
        set => $this->occurredAt = $value;
    }

    public bool $isBot = false {
        get => $this->isBot;
        set => $this->isBot = $value;
    }

    public ?DateTimeImmutable $createdAt = null {
        get => $this->createdAt;
        set => $this->createdAt = $value;
    }

    /**
     * @param array<string, mixed>|null $payload
     */
    public function __construct(
        int $visitorId,
        int $sessionId,
        ActionType $actionType,
        DateTimeImmutable $occurredAt,
        ?array $payload = null,
    ) {
        $this->visitorId = $visitorId;
        $this->sessionId = $sessionId;
        $this->actionType = $actionType;
        $this->occurredAt = $occurredAt;
        $this->payload = $payload;
    }

    public function markAsBot(): void
    {
        $this->isBot = true;
    }
}
