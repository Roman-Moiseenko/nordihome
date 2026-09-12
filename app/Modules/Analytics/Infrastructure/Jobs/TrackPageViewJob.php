<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Jobs;

use App\Modules\Analytics\Application\Actions\Visitor\ResolveGeoIpUseCase;
use App\Modules\Analytics\Application\DTOs\VisitSnapshot;
use App\Modules\Analytics\Infrastructure\Services\GeoIpResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * TrackPageViewJob — асинхронное дополнение к синхронной фиксации просмотра.
 *
 * Посетитель, сессия и page_view создаются синхронно в TrackPageViewMiddleware
 * (чтобы page_view_id был доступен в VisitorContext). Сюда вынесены тяжёлые
 * операции: GeoIP-разрешение (только при первом касании) и запись шага пути.
 */
final class TrackPageViewJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly VisitSnapshot $snapshot,
        public readonly string $pageType,
        public readonly ?int $entityId,
        public readonly string $url,
        public readonly int $visitorId,
        public readonly int $sessionId,
        public readonly bool $needGeo,
    ) {}

    public function handle(
        ResolveGeoIpUseCase $resolveGeoIp,
        GeoIpResolver $geoIpResolver,
    ): void {
        if ($this->snapshot->isBot) {
            return;
        }

        // Гео-данные определяем один раз — при первом касании (когда ещё нет гео).
        if ($this->needGeo) {
            [$city, $region, $country] = $geoIpResolver->resolve($this->snapshot->ip);

            if ($city !== null || $region !== null || $country !== null) {
                $resolveGeoIp->execute($this->visitorId, $city, $region, $country);
            }
        }
    }
}
