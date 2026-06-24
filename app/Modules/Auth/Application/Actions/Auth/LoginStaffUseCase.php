<?php

namespace App\Modules\Auth\Application\Actions\Auth;

use App\Modules\Auth\Application\DTOs\LoginData;
use App\Modules\Auth\Application\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Domain\Services\PasswordHasherInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Infrastructure\Models\User;
use Illuminate\Support\Facades\Auth;

readonly class LoginStaffUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository,
    private PasswordHasherInterface                             $passwordHasher,) {}

    public function execute(LoginData $dto): string
    {
        $email = new Email($dto->email);
        $user = $this->userRepository->findByEmail($email);


        if (!$user || !$user->validatePassword($dto->password, $this->passwordHasher))
            throw new InvalidCredentialsException('Неверный email или пароль');

        if (!$user->hasRole('admin') && !$user->hasRole('staff'))
            throw new \DomainException('Доступ разрешён только сотрудникам');

        if ($user->isBanned)
            throw new \DomainException('Ваш аккаунт заблокирован');

        Auth::attempt(['email' => $email, 'password' => $dto->password], $dto->remember);

        return $user->profileable?->fullName ?? $user->email;

    }
}
