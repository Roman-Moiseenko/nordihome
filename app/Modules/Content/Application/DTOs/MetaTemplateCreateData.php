<?php

namespace App\Modules\Content\Application\DTOs;

use Spatie\LaravelData\Data;

class MetaTemplateCreateData extends Data
{
    public function __construct(
        public readonly string $class,
        public readonly ?string $entity = null,
    )
    {

    }
}
