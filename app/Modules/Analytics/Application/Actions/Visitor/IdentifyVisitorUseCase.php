<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Visitor;

use App\Modules\Analytics\Domain\Entities\VisitorEntity;
use App\Modules\Analytics\Domain\Interfaces\VisitorContextInterface;
use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;
use App\Modules\Analytics\Domain\ValueObjects\TrafficSource;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;
use DateTimeImmutable;

/**
 * IdentifyVisitor — идентификация посетителя по UUID из cookie.
 *
 * Если посетителя нет — создаёт нового со снимком первого касания (ip, user-agent,
 * источник трафика, utm-метки, посадочная страница). Если посетитель уже есть —
 * фиксирует новый визит (visits_count + last_visit_at) и при необходимости
 * привязывает к клиенту. Результат кладёт в VisitorContext.
 */
final readonly class IdentifyVisitorUseCase
{
    public function __construct(
        private VisitorRepositoryInterface $visitors,
        private VisitorContextInterface    $context,
    ) {}

    /**
     * @param array<string, string|null> $utm
     */
    public function execute(
        string $uuid,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $referrer = null,
        array $utm = [],
        ?string $landingUrl = null,
        ?int $clientId = null,
        ?string $deviceType = null,
        ?string $os = null,
        ?string $browser = null,
    ): VisitorEntity {
        $uuidVo = new VisitorUuid($uuid);
        $now = new DateTimeImmutable();

        $visitor = $this->visitors->findByUuid($uuidVo);

        if ($visitor === null) {
            $visitor = new VisitorEntity(
                (string) $uuidVo,
                $now,
                $now,
                TrafficSource::fromReferrer($referrer ?? '', $utm),
            );

            $visitor->ip = $ip;
            $visitor->userAgent = $userAgent;
            $visitor->deviceType = $deviceType;
            $visitor->os = $os;
            $visitor->browser = $browser;
            $visitor->referrer = $referrer;
            $visitor->utmSource = $utm['utm_source'] ?? null;
            $visitor->utmMedium = $utm['utm_medium'] ?? null;
            $visitor->utmCampaign = $utm['utm_campaign'] ?? null;
            $visitor->utmTerm = $utm['utm_term'] ?? null;
            $visitor->utmContent = $utm['utm_content'] ?? null;
            $visitor->landingUrl = $landingUrl;
            $visitor->isBot = self::isBot($userAgent);

            if ($clientId !== null) {
                $visitor->linkClient($clientId, $now);
            }

            $visitor = $this->visitors->create($visitor);
        } else {
            $visitor->recordVisit($now);

            if ($clientId !== null && $visitor->clientId === null) {
                $visitor->linkClient($clientId, $now);
            }

            if (!$visitor->isBot && self::isBot($userAgent)) {
                $visitor->markAsBot();
            }

            $this->visitors->update($visitor);
        }

        if ($visitor->id !== null) {
            $this->context->setVisitorId($visitor->id);
        }

        return $visitor;
    }

    /**
     * Грубое определение бота по User-Agent на этапе идентификации.
     */
    private static function isBot(?string $userAgent): bool
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return false;
        }

        return preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|whatsapp|monitor/i', $userAgent) === 1;
    }
}
