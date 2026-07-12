<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\DTOs\Elements;

class UrlData
{
    public function __construct(
        public readonly string $url,
        public readonly string $name,
    ) {}
}
