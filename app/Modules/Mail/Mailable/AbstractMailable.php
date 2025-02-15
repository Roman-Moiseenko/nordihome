<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\Setting\Entity\Mail;
use App\Modules\Setting\Entity\Settings;
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
    protected Mail $mail_settings;

    public function __construct()
    {
        $this->files = [];
        /** @var Settings $settings */
        $settings = app()->make(Settings::class);
        $this->mail_settings =$settings->mail;
    }

    abstract public function envelope(): Envelope;
    #[Pure]
    abstract public function content(): Content;
    abstract public function attachments(): array;
    abstract public function getFiles(): array;


}
