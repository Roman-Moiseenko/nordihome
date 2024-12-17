<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\User\Entity\User;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class VerifyMail extends SystemMailable
{

    private string $verify_token;

    public function __construct(string $verify_token)
    {
        parent::__construct();
        $this->subject = 'Подтверждение почты при регистрации';
        $this->verify_token = $verify_token;
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.verify',
            with: [
                'verify_token' => $this->verify_token
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
