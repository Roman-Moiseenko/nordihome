<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Services;

use Illuminate\Support\Facades\Http;

/**
 * GeoIpResolver — определение города/региона/страны по IP.
 *
 * Использует бесплатный endpoint ip-api.com (без ключа). Возвращает
 * [city, region, country(ISO2)] либо null-ы при любой ошибке. Локальные и
 * зарезервированные IP пропускаются, чтобы не дёргать сервис впустую.
 */
final class GeoIpResolver
{
    private const string ENDPOINT = 'http://ip-api.com/json/';

    /**
     * @return array{0: ?string, 1: ?string, 2: ?string}
     */
    public function resolve(string $ip): array
    {
        if ($ip === '' || $this->isPrivateIp($ip)) {
            return [null, null, null];
        }

        try {
            $response = Http::timeout(3)
                ->get(self::ENDPOINT . $ip, [
                    'fields' => 'status,message,countryCode,regionName,city',
                ]);

            if (!$response->successful()) {
                return [null, null, null];
            }

            $data = $response->json();

            if (($data['status'] ?? null) !== 'success') {
                return [null, null, null];
            }

            return [
                $this->nullableString($data['city'] ?? null),
                $this->nullableString($data['regionName'] ?? null),
                $this->nullableString($data['countryCode'] ?? null),
            ];
        } catch (\Throwable) {
            return [null, null, null];
        }
    }

    private function isPrivateIp(string $ip): bool
    {
        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        ) === false && filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    private function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }
}
