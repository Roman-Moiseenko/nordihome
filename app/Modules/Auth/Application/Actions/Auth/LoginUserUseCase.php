<?php

namespace App\Modules\Auth\Application\Actions\Auth;

use App\Modules\Auth\Application\DTOs\LoginData;
use App\Modules\Auth\Domain\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\Services\PasswordHasherInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use Illuminate\Support\Facades\Auth;

readonly class LoginUserUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository,
    private PasswordHasherInterface                             $passwordHasher,
    ) {}

    public function execute(LoginData $dto): bool
    {
        $email = new Email($dto->email);
        $user = $this->userRepository->findByEmail($email);
        if (!$user || !$user->validatePassword($dto->password, $this->passwordHasher)) return false;

        if (!$user->hasRole('client') && !$user->hasRole('staff')) return false;

        Auth::attempt(['email' => $email, 'password' => $dto->password], $dto->remember);
        return true;
    }
}
