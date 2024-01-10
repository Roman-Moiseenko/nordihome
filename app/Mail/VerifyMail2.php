<?php

namespace App\Mail;


use App\Modules\User\Entity\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyMail2 extends Mailable
{
    use Queueable, SerializesModels;

    public string $key;

    /**
     * Create a new message instance.
     * @param User $user
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'VerifyMail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('Подтверждение')
            ->markdown('mail.verify-mail')->with(['url' => route('register.verify', ['token' => $this->key])]);
    }
}
