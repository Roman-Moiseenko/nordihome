<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\User\Entity\User;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class VerifyMail extends SystemMailable
{

    private string $verify_token;
    private string $login;

    public function __construct(array $data)
    {
        parent::__construct();
        $this->subject = 'Подтверждение почты при регистрации';
        $this->verify_token = (string)$data['token'];
        $this->login = $data['login'];
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.verify',
            with: [
                'verify_token' => $this->verify_token,
                'login' => $this->login,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function getFiles(): array
    {
        return [];
    }
}
