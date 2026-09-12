<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Services;

use App\Modules\Analytics\Domain\Interfaces\VisitorContextInterface;

/**
 * VisitorContext — scoped-реализация контекста текущего запроса.
 *
 * Хранит visitor_id, session_id и page_view_id в свойствах объекта, чтобы
 * не выполнять повторные запросы в рамках одного HTTP-запроса.
 */
class VisitorContext implements VisitorContextInterface
{
    private ?int $visitorId = null;
    private ?int $sessionId = null;
    private ?int $pageViewId = null;

    public function getVisitorId(): ?int
    {
        return $this->visitorId;
    }

    public function getSessionId(): ?int
    {
        return $this->sessionId;
    }

    public function getPageViewId(): ?int
    {
        return $this->pageViewId;
    }

    public function setVisitorId(int $visitorId): void
    {
        $this->visitorId = $visitorId;
    }

    public function setSessionId(int $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function setPageViewId(int $pageViewId): void
    {
        $this->pageViewId = $pageViewId;
    }
}
