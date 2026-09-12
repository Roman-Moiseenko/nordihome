<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\DTOs;

use App\Modules\Analytics\Domain\ValueObjects\DeviceData;
use App\Modules\Analytics\Domain\ValueObjects\UtmData;
use App\Modules\Analytics\Domain\ValueObjects\VisitorUuid;

/**
 * VisitSnapshot — снимок данных визита, собранный на этапе middleware.
 *
 * Не содержит результатов обращений к БД: только данные запроса и cookie.
 * Используется для передачи в очереди (TrackPageViewJob, LinkVisitorToClientJob).
 */
final class VisitSnapshot
{
    public function __construct(
        public readonly VisitorUuid $uuid,
        public readonly ?int $clientId,
        public readonly string $ip,
        public readonly string $userAgent,
        public readonly ?string $referrer,
        public readonly UtmData $utm,
        public readonly DeviceData $device,
        public readonly bool $isBot,
        public readonly string $url,
        public readonly bool $isNewUuid,
    ) {
    }
}
