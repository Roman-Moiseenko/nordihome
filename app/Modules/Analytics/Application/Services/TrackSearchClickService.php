<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Services;

use App\Modules\Analytics\Application\Actions\Search\RegisterSearchClickUseCase;
use App\Modules\Analytics\Application\Actions\Search\TrackSearchUseCase;
use App\Modules\Analytics\Application\DTOs\Search\TrackSearchClickData;
use App\Modules\Analytics\Domain\Entities\SessionEntity;
use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use DateTimeImmutable;

/**
 * TrackSearchClick — фиксация клика по результату поисковой выдачи.
 *
 * Сначала регистрирует строку поиска через TrackSearchUseCase, затем дополняет
 * созданную запись информацией о выбранном результате через
 * RegisterSearchClickUseCase. Идентификация — по uuid из cookie.
 */
final readonly class TrackSearchClickService
{
    public function __construct(
        private VisitorRepositoryInterface $visitors,
        private SessionRepositoryInterface $sessions,
        private TrackSearchUseCase $trackSearch,
        private RegisterSearchClickUseCase $registerClick,
    ) {}

    public function execute(TrackSearchClickData $dto, ?string $uuid = null): bool
    {
        $visitor = $this->resolveVisitor($uuid);
        if ($visitor === null || $visitor->id === null) {
            return false;
        }

        $session = $this->findActiveSession($visitor->id);

        $search = $this->trackSearch->execute(
            $visitor->id,
            $session?->id,
            $dto->pageViewId,
            $dto->query,
        );

        if ($search->id === null) {
            return false;
        }

        $this->registerClick->execute(
            $search->id,
            $dto->resultId,
            $dto->resultType,
            $dto->position,
        );

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
