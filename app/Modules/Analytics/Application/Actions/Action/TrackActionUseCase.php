<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Action;

use App\Modules\Analytics\Application\DTOs\Action\TrackActionData;
use App\Modules\Analytics\Domain\Entities\ActionEntity;
use App\Modules\Analytics\Domain\Entities\SessionEntity;
use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\Interfaces\ActionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\PageViewRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
use App\Modules\Analytics\Domain\ValueObjects\ActionType;
use App\Modules\Analytics\Domain\ValueObjects\EntityType;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use DateTimeImmutable;

/**
 * TrackAction — регистрация действия клиента (add_to_cart, click_buy, ...).
 *
 * Все проверки (наличие посетителя и активной сессии) выполняются внутри.
 * Возвращает false, если событие невозможно зафиксировать.
 */
final readonly class TrackActionUseCase
{
    public function __construct(
        private VisitorRepositoryInterface $visitors,
        private SessionRepositoryInterface $sessions,
        private ActionRepositoryInterface $actions,
        private PageViewRepositoryInterface $pageViews,
    ) {}

    public function execute(TrackActionData $dto, ?string $uuid = null): bool
    {
        $visitor = $this->resolveVisitor($uuid);
        if ($visitor === null || $visitor->id === null) {
            return false;
        }

        // Если активная сессия была закрыта exit-трекером (например, при
        // переключении вкладки) — создаём новую, чтобы действие не потерялось.
        $session = $this->ensureSession($visitor);
        if ($session->id === null) {
            return false;
        }

        $now = new DateTimeImmutable();

        $action = new ActionEntity(
            $visitor->id,
            $session->id,
            ActionType::from($dto->actionType),
            $now,
            $dto->payload,
        );

        $action->entityType = $dto->entityType !== null ? EntityType::from($dto->entityType) : null;
        $action->entityId = $dto->entityId;
        $action->pageViewId = $this->resolvePageViewId($dto->pageViewId, $visitor->id, $session->id);

        $this->actions->create($action);

        $this->sessions->increment($session->id, 'actions_count');
        $this->sessions->touch($session->id, $now);

        return true;
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

    /**
     * Возвращает активную сессию или создаёт новую, если предыдущая была
     * закрыта exit-трекером (переключение вкладки, уход со страницы).
     */
    private function ensureSession(VisitorEntity $visitor): SessionEntity
    {
        $active = $this->findActiveSession($visitor->id);
        if ($active !== null) {
            return $active;
        }

        $now = new DateTimeImmutable();
        $url = (string) request()->url();

        $session = new SessionEntity(
            $visitor->id,
            $now,
            $now,
            $url,
            'page',
        );

        $session->referrer = request()->headers->get('referer');
        $session->ip = (string) request()->ip();
        $session->userAgent = (string) request()->userAgent();

        return $this->sessions->create($session);
    }

    /**
     * page_view_id действия: если явно не передан — берём последний просмотр
     * текущей сессии (действие происходит на открытой странице).
     */
    private function resolvePageViewId(?int $pageViewId, int $visitorId, int $sessionId): ?int
    {
        if ($pageViewId !== null) {
            return $pageViewId;
        }

        return $this->pageViews->findLastByVisitorInSession($visitorId, $sessionId)?->id;
    }

    private function timeoutMinutes(): int
    {
        return (int) config('analytics.session_timeout_minutes', 30);
    }
}
