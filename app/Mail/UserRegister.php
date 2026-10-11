<?php

namespace App\Mail;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Discount\Entity\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegister extends Mailable
{
    use Queueable, SerializesModels;

    private Client $client;
    private Coupon $coupon;

    /**
     * Create a new message instance.
     */
    public function __construct(Client $user, Coupon $coupon)
    {
        //
        $this->client = $user;
        $this->coupon = $coupon;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Регистрация на NORDI HOME',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.register',
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
        return $this->subject('Регистрация')
            ->markdown('mail.user.register')->with([ 'user' => $this->client, 'coupon' => $this->coupon]);
    }
}
