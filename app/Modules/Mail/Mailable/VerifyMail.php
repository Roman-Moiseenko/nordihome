<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\User\Entity\User;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class VerifyMail extends SystemMailable
{

    private string $verify_token;
    private string $email;

    public function __construct(array $data)
    {
        parent::__construct();
        $this->subject = 'Подтверждение почты при регистрации';
        $this->verify_token = $data['token'];
        $this->email = $data['email'];
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.verify',
            with: [
                'verify_token' => $this->verify_token,
                'email' => $this->email,
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
