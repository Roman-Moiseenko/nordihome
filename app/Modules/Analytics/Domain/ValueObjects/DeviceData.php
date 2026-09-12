<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\ValueObjects;

/**
 * DeviceData — результат парсинга User-Agent: тип устройства, ОС и браузер.
 */
final class DeviceData
{
    public function __construct(
        public readonly ?string $deviceType = null,
        public readonly ?string $os = null,
        public readonly ?string $browser = null,
    ) {
    }
}
