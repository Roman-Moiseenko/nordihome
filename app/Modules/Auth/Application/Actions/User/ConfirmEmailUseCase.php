<?php

namespace App\Modules\Auth\Application\Actions\User;

use App\Modules\Auth\Application\Actions\Client\ConsentClientUseCase;
use App\Modules\Auth\Domain\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use InvalidArgumentException;

/**
 * Подтверждение смены почты
 * Доступ не проверяется
 */
readonly class ConfirmEmailUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ConsentClientUseCase $consentClientUseCase,
    ) {}

    public function execute(string $token, bool $agreement): void
    {
        $verification = $this->userRepository->findEmailVerificationByToken($token);
        if (!$verification || now()->gt($verification->expires_at)) {
            throw new InvalidArgumentException('Токен недействителен или срок его действия истёк');
        }

        if (!$agreement) throw new InvalidArgumentException('Нет согласия');

        $user = $this->userRepository->findById($verification->user_id);
        if (!$user) {
            throw new InvalidArgumentException('Пользователь не найден');
        }

        // Если email в профиле пользователя совпадает с new_email, это первичная верификация
        if ((string)$user->email === $verification->new_email) {
            $user->verifyEmail(); // устанавливает email_verified_at
        } else {
            // Смена email: обновляем email и отмечаем как подтверждённый
            $user->email = new Email($verification->new_email);
            $user->verifyEmail();
        }

        $this->consentClientUseCase->execute($user->profileableId); //Согласие

        $this->userRepository->save($user);
        $this->userRepository->deleteEmailVerification($token);
    }
}
