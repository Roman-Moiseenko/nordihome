<?php

namespace App\Modules\Page\Application\DTOs;

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
