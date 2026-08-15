<?php

namespace App\Modules\Auth\Application\Actions\Password;

use App\Modules\Auth\Application\Interfaces\PasswordResetTokenRepositoryInterface;

readonly class ValidatePasswordResetTokenUseCase
{
    public function __construct(
        private PasswordResetTokenRepositoryInterface $tokenRepo,
    ) {}

    public function execute(string $token): bool
    {
        return $this->tokenRepo->findValid($token) !== null;
    }
}
