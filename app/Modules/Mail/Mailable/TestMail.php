<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\Pure;

class TestMail extends SystemMailable
{

    public function __construct()
    {
        parent::__construct();
        $path = storage_path('app/files/order/0/');
        $this->files = [
            'test-01.txt' => $path . 'test-01.txt',
            'test-02.txt' => $path . 'test-02.txt',
        ];
        $this->subject = 'Служебное письмо';
    }


    #[Pure]
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.test',
            with: [
            ],
        );
    }

    public function attachments(): array
    {
        return array_map(function ($item) {
                return Attachment::fromPath($item);
            }, $this->files);
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
