<?php

namespace App\Modules\Content\Application\DTOs\Widget;

use Spatie\LaravelData\Data;

class WidgetContentUpdateData extends Data
{
    public function __construct(
        public readonly string $content,
    ) {}
}
