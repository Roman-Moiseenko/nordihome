<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use DateTimeImmutable;

/**
 * Exit — точка выхода из сессии (для анализа страниц, на которых посетители
 * покидают сайт).
 */
final class ExitEntity
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

    public int $pageViewId {
        get => $this->pageViewId;
        set => $this->pageViewId = $value;
    }

    public string $url {
        get => $this->url;
        set => $this->url = $value;
    }

    public string $pageType {
        get => $this->pageType;
        set => $this->pageType = $value;
    }

    public ?int $entityId = null {
        get => $this->entityId;
        set => $this->entityId = $value;
    }

    public DateTimeImmutable $exitAt {
        get => $this->exitAt;
        set => $this->exitAt = $value;
    }

    public int $durationOnPage {
        get => $this->durationOnPage;
        set => $this->durationOnPage = $value;
    }

    public ?string $reason = null {
        get => $this->reason;
        set => $this->reason = $value;
    }

    public function __construct(
        int $visitorId,
        int $sessionId,
        int $pageViewId,
        string $url,
        string $pageType,
        DateTimeImmutable $exitAt,
        int $durationOnPage,
        ?string $reason = null,
    ) {
        $this->visitorId = $visitorId;
        $this->sessionId = $sessionId;
        $this->pageViewId = $pageViewId;
        $this->url = $url;
        $this->pageType = $pageType;
        $this->exitAt = $exitAt;
        $this->durationOnPage = $durationOnPage;
        $this->reason = $reason;
    }
}
