<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Entities;

use DateTimeImmutable;

/**
 * PageView — просмотр страницы в рамках сессии посетителя.
 *
 * Событийная (append-only) запись: фиксирует факт просмотра страницы, её тип,
 * URL и метрики (длительность, глубина прокрутки), а также флаги входа/выхода
 * из сессии. Связана с VisitorEntity и SessionEntity.
 */
final class PageViewEntity
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

    public string $path {
        get => $this->path;
        set => $this->path = $value;
    }

    public ?string $title = null {
        get => $this->title;
        set => $this->title = $value;
    }

    public ?string $referrer = null {
        get => $this->referrer;
        set => $this->referrer = $value;
    }

    public DateTimeImmutable $viewedAt {
        get => $this->viewedAt;
        set => $this->viewedAt = $value;
    }

    public ?int $duration = null {
        get => $this->duration;
        set => $this->duration = $value;
    }

    public ?int $scrollDepth = null {
        get => $this->scrollDepth;
        set => $this->scrollDepth = $value;
    }

    public bool $isEntry = false {
        get => $this->isEntry;
        set => $this->isEntry = $value;
    }

    public bool $isExit = false {
        get => $this->isExit;
        set => $this->isExit = $value;
    }

    public bool $isBounce = false {
        get => $this->isBounce;
        set => $this->isBounce = $value;
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
        int $sessionId,
        string $pageType,
        string $url,
        string $path,
        DateTimeImmutable $viewedAt,
    ) {
        $this->visitorId = $visitorId;
        $this->sessionId = $sessionId;
        $this->pageType = $pageType;
        $this->url = $url;
        $this->path = $path;
        $this->viewedAt = $viewedAt;
    }

    public function markAsEntry(): void
    {
        $this->isEntry = true;
    }

    public function markAsExit(): void
    {
        $this->isExit = true;
    }

    public function markAsBounce(): void
    {
        $this->isBounce = true;
    }

    public function markAsBot(): void
    {
        $this->isBot = true;
    }

    /**
     * Фиксирует время на странице в секундах.
     */
    public function trackDuration(int $seconds): void
    {
        $this->duration = max(0, $seconds);
    }

    /**
     * Фиксирует глубину прокрутки в процентах (0–100).
     */
    public function trackScrollDepth(int $percent): void
    {
        $this->scrollDepth = max(0, min(100, $percent));
    }
}
