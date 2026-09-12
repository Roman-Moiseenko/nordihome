<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Exit;

use App\Modules\Analytics\Application\DTOs\Exit\RecordExitData;
use App\Modules\Analytics\Domain\Entities\ExitEntity;
use App\Modules\Analytics\Domain\Entities\PageViewEntity;
use App\Modules\Analytics\Domain\Entities\SessionEntity;
use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\Interfaces\ExitRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\PageViewRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\PathRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use DateTimeImmutable;

/**
 * RecordExit — запись точки выхода из сессии.
 *
 * Все проверки (наличие посетителя, активной сессии и последнего просмотра)
 * выполняются внутри. Возвращает false, если выход невозможно зафиксировать.
 */
final readonly class RecordExitUseCase
{
    public function __construct(
        private VisitorRepositoryInterface $visitors,
        private SessionRepositoryInterface $sessions,
        private PageViewRepositoryInterface $pageViews,
        private ExitRepositoryInterface $exits,
        private PathRepositoryInterface $paths,
    ) {}

    public function execute(RecordExitData $dto, ?string $uuid = null): bool
    {
        $visitor = $this->resolveVisitor($uuid);
        if ($visitor === null || $visitor->id === null) {
            return false;
        }

        $session = $this->findActiveSession($visitor->id);
        if ($session === null) {
            return false;
        }

        $pageView = $this->resolvePageView($dto->pageViewId, $visitor->id, $session->id);
        if ($pageView === null || $pageView->id === null) {
            return false;
        }

        $duration = max(0, $dto->duration);
        $now = new DateTimeImmutable();

        // Фиксируем метрики просмотра: длительность, глубину прокрутки и признак выхода.
        $this->pageViews->finalizeView($pageView->id, $duration, $dto->scrollDepth, true);

        // Проставляем длительность последнему шагу пути клиента.
        $this->paths->updateLastDuration($session->id, $duration);

        // Bounce — единственная страница в сессии (посетитель зашёл и сразу ушёл).
        $isBounce = $this->pageViews->countBySession($session->id) === 1;
        if ($isBounce) {
            $this->pageViews->markAsBounce($pageView->id);
        }

        // Закрываем сессию: ended_at, duration, страница выхода.
        $session->finish($now);
        $session->exitUrl = $dto->url ?? $pageView->url;
        $session->exitPageType = $dto->pageType ?? $pageView->pageType;
        $session->exitEntityId = $dto->entityId ?? $pageView->entityId;
        $session->isBounce = $isBounce;

        $this->sessions->update($session);

        $exit = new ExitEntity(
            $visitor->id,
            $session->id,
            $pageView->id,
            $dto->url ?? $pageView->url,
            $dto->pageType ?? $pageView->pageType,
            $now,
            $duration,
            $dto->reason,
        );

        $exit->entityId = $dto->entityId ?? $pageView->entityId;

        $this->exits->create($exit);

        return true;
    }

    private function resolvePageView(?int $pageViewId, int $visitorId, int $sessionId): ?PageViewEntity
    {
        if ($pageViewId !== null) {
            $view = $this->pageViews->findById($pageViewId);
            if ($view !== null && $view->sessionId === $sessionId) {
                return $view;
            }
        }

        return $this->pageViews->findLastByVisitorInSession($visitorId, $sessionId);
    }

    private function resolveVisitor(?string $uuid): ?VisitorEntity
    {
        if ($uuid === null || $uuid === '') {
            return null;
        }

        return $this->visitors->findByUuid(new VisitorUuid($uuid));
    }

    private function findActiveSession(int $visitorId): ?SessionEntity
    {
        $window = (new DateTimeImmutable())->modify('-' . $this->timeoutMinutes() . ' minutes');

        return $this->sessions->findActiveByVisitor($visitorId, $window);
    }

    private function timeoutMinutes(): int
    {
        return (int) config('analytics.session_timeout_minutes', 30);
    }
}
