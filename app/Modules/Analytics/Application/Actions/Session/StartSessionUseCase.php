<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Session;

use App\Modules\Analytics\Domain\Entities\SessionEntity;
use App\Modules\Analytics\Domain\Interfaces\SessionRepositoryInterface;
use App\Modules\Analytics\Domain\Interfaces\VisitorContextInterface;
use App\Modules\Analytics\Domain\ValueObjects\TrafficSource;
use DateTimeImmutable;

/**
 * StartSession — открытие визита (сессии) посетителя.
 *
 * Если у посетителя есть активная сессия (в пределах таймаута) — просто
 * продлевает её. Иначе создаёт новую сессию с данными страницы входа.
 * Результат кладёт в VisitorContext.
 */
final readonly class StartSessionUseCase
{
    public function __construct(
        private SessionRepositoryInterface $sessions,
        private VisitorContextInterface    $context,
    ) {}

    /**
     * @param array<string, string|null> $utm
     */
    public function execute(
        int $visitorId,
        string $entryUrl,
        string $entryPageType,
        ?int $entryEntityId = null,
        ?string $referrer = null,
        array $utm = [],
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $deviceType = null,
        ?string $os = null,
        ?string $browser = null,
    ): SessionEntity {
        $now = new DateTimeImmutable();
        $window = $now->modify('-' . $this->sessionTimeoutMinutes() . ' minutes');

        $active = $this->sessions->findActiveByVisitor($visitorId, $window);

        if ($active !== null) {
            $active->touch($now);

            // Первое касание с utm-метками: если сессия ещё не имеет utm —
            // заполняем из текущего запроса.
            $this->fillMissingUtm($active, $utm);

            if ($active->id !== null) {
                $this->sessions->update($active);
                $this->context->setSessionId($active->id);
            }

            return $active;
        }

        $session = new SessionEntity(
            $visitorId,
            $now,
            $now,
            $entryUrl,
            $entryPageType,
            TrafficSource::fromReferrer($referrer ?? '', $utm),
        );

        $session->entryEntityId = $entryEntityId;
        $session->referrer = $referrer;
        $session->utmSource = $utm['utm_source'] ?? null;
        $session->utmMedium = $utm['utm_medium'] ?? null;
        $session->utmCampaign = $utm['utm_campaign'] ?? null;
        $session->utmTerm = $utm['utm_term'] ?? null;
        $session->utmContent = $utm['utm_content'] ?? null;
        $session->ip = $ip;
        $session->userAgent = $userAgent;
        $session->deviceType = $deviceType;
        $session->os = $os;
        $session->browser = $browser;

        $session = $this->sessions->create($session);

        if ($session->id !== null) {
            $this->context->setSessionId($session->id);
        }

        return $session;
    }

    /**
     * Заполняет utm-метки сессии, если они ещё не были зафиксированы
     * (first-touch: не перезаписываем уже сохранённые значения).
     *
     * @param array<string, string|null> $utm
     */
    private function fillMissingUtm(SessionEntity $session, array $utm): void
    {
        $session->utmSource ??= $utm['utm_source'] ?? null;
        $session->utmMedium ??= $utm['utm_medium'] ?? null;
        $session->utmCampaign ??= $utm['utm_campaign'] ?? null;
        $session->utmTerm ??= $utm['utm_term'] ?? null;
        $session->utmContent ??= $utm['utm_content'] ?? null;
    }

    private function sessionTimeoutMinutes(): int
    {
        return (int) config('analytics.session_timeout_minutes', 30);
    }
}
