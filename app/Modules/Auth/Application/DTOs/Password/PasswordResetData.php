<?php

namespace App\Modules\Auth\Application\DTOs\Password;

final readonly class PasswordResetData
{
    public function __construct(
        public string $email,
        public string $token,
        public \DateTimeImmutable $createdAt,
    ) {}
}
