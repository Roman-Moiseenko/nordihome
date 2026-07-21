<?php

declare(strict_types=1);

namespace App\Modules\Feedback\Application\DTOs\FormBack;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Data;

class FormBackCreateData extends Data
{
    public function __construct(
        #[Required, StringType, Url]
        public readonly string $url,

        #[Required]
        public readonly array $data,
    ) {}
}
