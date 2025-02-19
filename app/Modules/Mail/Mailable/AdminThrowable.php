<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use JetBrains\PhpStorm\Pure;

class AdminThrowable extends SystemMailable
{
    private \Throwable $throwable;

    public function __construct(\Throwable $throwable)
    {
        parent::__construct();
        $this->subject = 'Подтверждение почты при регистрации';
        $this->throwable = $throwable;
    }


    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.admin.throwable',
            with: [
                'throwable' => $this->throwable
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

    public function getName(): string
    {
        return 'Фатальная ошибка на сайте';
    }
}
