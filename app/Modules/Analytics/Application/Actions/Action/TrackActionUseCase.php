<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Action;

use App\Modules\Analytics\Application\DTOs\Action\TrackActionData;
use App\Modules\Analytics\Domain\Entities\ActionEntity;
use App\Modules\Analytics\Domain\Entities\SessionEntity;
use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\Interfaces\ActionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
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
    ) {}

    public function execute(TrackActionData $dto, ?string $uuid = null): bool
    {
        $visitor = $this->resolveVisitor($uuid);
        if ($visitor === null || $visitor->id === null) {
            return false;
        }

        $session = $this->findActiveSession($visitor->id);
        if ($session === null) {
            return false;
        }

        $now = new DateTimeImmutable();

        $action = new ActionEntity(
            $visitor->id,
            $session->id,
            $dto->actionType,
            $now,
            $dto->payload,
        );

        $action->entityType = $dto->entityType;
        $action->entityId = $dto->entityId;
        $action->pageViewId = $dto->pageViewId;

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

    private function timeoutMinutes(): int
    {
        return (int) config('analytics.session_timeout_minutes', 30);
    }
}
