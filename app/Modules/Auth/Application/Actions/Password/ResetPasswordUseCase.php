<?php

namespace App\Modules\Auth\Application\Actions\Password;

use App\Modules\Auth\Domain\Interfaces\PasswordResetTokenRepositoryInterface;
use App\Modules\Auth\Domain\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\Services\PasswordHasherInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Auth\Domain\ValueObjects\HashedPassword;

readonly class ResetPasswordUseCase
{
    public function __construct(
        private PasswordResetTokenRepositoryInterface $tokenRepo,
        private UserRepositoryInterface $userRepo,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function execute(string $token, string $newPassword): void
    {
        $reset = $this->tokenRepo->findValid($token);
        if (!$reset) throw new \InvalidArgumentException('Недействительный токен');

        $userEntity = $this->userRepo->findByEmail(new Email($reset->email));
        if (!$userEntity) throw new \InvalidArgumentException('Клиент не найден');
        if (!$userEntity->isClient()) return; //Не сообщаем, что нашли с таким email, не клиента


        $userEntity->updatePassword(HashedPassword::fromPlainText($newPassword, $this->passwordHasher));
        $this->userRepo->save($userEntity);

        $this->tokenRepo->delete($token);
    }
}
