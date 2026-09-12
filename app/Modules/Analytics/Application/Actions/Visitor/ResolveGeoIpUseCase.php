<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Application\Actions\Visitor;

use App\Modules\Analytics\Domain\Interfaces\VisitorRepositoryInterface;

/**
 * ResolveGeoIp — запись гео-данных посетителя.
 *
 * Вызывается асинхронно после определения города/региона/страны GeoIP-сервисом.
 * Обновляет только гео-поля, не затрагивая снимок первого касания.
 */
final readonly class ResolveGeoIpUseCase
{
    public function __construct(
        private VisitorRepositoryInterface $visitors,
    ) {}

    public function execute(
        int $visitorId,
        ?string $city,
        ?string $region,
        ?string $country,
    ): void {
        $this->visitors->setGeoData($visitorId, $city, $region, $country);
    }
}
