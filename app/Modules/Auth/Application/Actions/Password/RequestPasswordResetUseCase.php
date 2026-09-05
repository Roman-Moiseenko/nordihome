<?php

namespace App\Modules\Auth\Application\Actions\Password;

use App\Modules\Auth\Domain\Interfaces\PasswordResetTokenRepositoryInterface;
use App\Modules\Auth\Domain\Interfaces\UserRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\Email;
use App\Modules\Mail\Entity\MailTemplateRegistry;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;

readonly class RequestPasswordResetUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepo,

        private PasswordResetTokenRepositoryInterface $tokenRepo,
        private MailServiceInterface $mailService,
    ) {}
    public function execute(string $email): void
    {
        $userEntity = $this->userRepo->findByEmail(new Email($email));
        if (!$userEntity || !$userEntity->isClient()) return; // тихо игнорируем, чтобы не раскрывать существование email

        $token = $this->tokenRepo->create($email);
        $resetUrl = route('password.reset', $token);

        $template = MailTemplateRegistry::get('user.password-reset');
        $this->mailService->send(
            $template,
            ['url' => $resetUrl],
            new Recipient(email: $email, clientId: $userEntity->profileableId) //Нужен именно Client::id
        );
    }
}
