<?php

declare(strict_types=1);

namespace App\Modules\Auth\Application\DTOs\Client;

use Spatie\LaravelData\Data;

class FindClientByContactData extends Data
{
    public function __construct(
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
    ) {}
}
