<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Infrastructure\Services;

use App\Modules\Analytics\Domain\ValueObjects\DeviceData;

/**
 * UserAgentParser — лёгкий парсер User-Agent без внешних зависимостей.
 *
 * Определяет тип устройства, ОС и браузер, а также признак бота.
 * Для точного разбора можно заменить на Jenssegers/Agent.
 */
final class UserAgentParser
{
    /**
     * @var array<string, string[]>
     */
    private const array BOT_MARKERS = [
        'bot', 'crawl', 'spider', 'slurp', 'bingpreview', 'facebookexternalhit',
        'whatsapp', 'monitor', 'headlesschrome', 'phantomjs', 'lighthouse', 'pingdom',
    ];

    public function parse(?string $userAgent): DeviceData
    {
        $ua = strtolower((string) $userAgent);

        if ($ua === '') {
            return new DeviceData();
        }

        return new DeviceData(
            deviceType: $this->detectDeviceType($ua),
            os: $this->detectOs($ua),
            browser: $this->detectBrowser($ua),
        );
    }

    public function isBot(?string $userAgent): bool
    {
        $ua = strtolower((string) $userAgent);

        if ($ua === '') {
            return false;
        }

        foreach (self::BOT_MARKERS as $marker) {
            if (str_contains($ua, $marker)) {
                return true;
            }
        }

        return false;
    }

    private function detectDeviceType(string $ua): ?string
    {
        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')) {
            return 'tablet';
        }

        if (str_contains($ua, 'mobi') || str_contains($ua, 'android')) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function detectOs(string $ua): ?string
    {
        return match (true) {
            str_contains($ua, 'windows') => 'windows',
            str_contains($ua, 'mac os') || str_contains($ua, 'macintosh') => 'macos',
            str_contains($ua, 'android') => 'android',
            str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ipod') => 'ios',
            str_contains($ua, 'linux') => 'linux',
            default => null,
        };
    }

    private function detectBrowser(string $ua): ?string
    {
        return match (true) {
            str_contains($ua, 'edg/') || str_contains($ua, 'edge/') => 'edge',
            str_contains($ua, 'opr/') || str_contains($ua, 'opera') => 'opera',
            str_contains($ua, 'yabrowser') => 'yandex',
            str_contains($ua, 'chrome') || str_contains($ua, 'crios') => 'chrome',
            str_contains($ua, 'firefox') || str_contains($ua, 'fxios') => 'firefox',
            str_contains($ua, 'safari') => 'safari',
            default => null,
        };
    }
}
