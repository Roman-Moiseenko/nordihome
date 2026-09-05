<?php

namespace App\Modules\Lead\Application\DTOs\Lead;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Symfony\Contracts\Service\Attribute\Required;

class LeadItemAddData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $comment,
        #[Nullable, StringType]
        public readonly ?string $finishedAt,
        #[Nullable, Numeric]
        public ?int $staffId = null, //Заполняем из контроллера
    )
    {

    }
}
