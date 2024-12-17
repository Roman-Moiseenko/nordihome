<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\Pure;

abstract class AbstractMailable extends Mailable
{
    use Queueable, SerializesModels;
    protected array $files;

    public function __construct()
    {
        $this->files = [];
    }

    abstract public function envelope(): Envelope;
    #[Pure]
    abstract public function content(): Content;
    abstract public function attachments(): array;
    abstract public function getFiles(): array;
}
