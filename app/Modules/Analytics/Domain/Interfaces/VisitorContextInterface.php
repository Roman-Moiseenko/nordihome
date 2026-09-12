<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

/**
 * VisitorContext — координация контекста текущего запроса.
 *
 * Держит visitor_id, session_id, page_view_id, чтобы не искать их повторно
 * в рамках одного запроса. Реализация — scoped-сервис в Infrastructure.
 */
interface VisitorContextInterface
{
    public function getVisitorId(): ?int;

    public function getSessionId(): ?int;

    public function getPageViewId(): ?int;

    public function setVisitorId(int $visitorId): void;

    public function setSessionId(int $sessionId): void;

    public function setPageViewId(int $pageViewId): void;
}
