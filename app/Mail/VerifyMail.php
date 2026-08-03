<?php

namespace App\Mail;


use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\User\Entity\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Crypto\DkimSigner;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Client $client;

    /**
     * Create a new message instance.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Подтверждение почты NORDI HOME',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        \Log::info('VerifyMail  Content  ' . $this->client->id);
        return new Content(
            markdown: 'mail.user.verify',
            with: [
                'user' => $this->client,
                'verify_token' => $this->client->verify_token,
                ]
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

    public function build(): VerifyMail
    {
        //$s = new DkimSigner();
        return $this->subject('Подтверждение')
            ->markdown('mail.user.verify')->with([ 'user' => $this->user]);
    }
}
