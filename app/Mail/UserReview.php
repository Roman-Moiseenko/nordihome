<?php

namespace App\Mail;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Setting\Entity\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserReview extends Mailable
{
    use Queueable, SerializesModels;

    private array $products;
    private Client $client;
    private int $bonus_amount;
    private bool $bonus_review;

    /**
     * Create a new message instance.
     */
    public function __construct(Client $client, array $products)
    {
        $settings = app()->make(Settings::class);
        $coupon = $settings->coupon;

        $this->products = $products;
        $this->client = $client;

        $this->bonus_amount = $coupon->bonus_amount;
        $this->bonus_review = $coupon->bonus_review;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Оставьте отзыв',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.review',
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
        return $this
            ->markdown('mail.user.review')
            ->with([ 'client' => $this->client, 'products' => $this->products, 'bonus_amount' => $this->bonus_amount, 'bonus_review' => $this->bonus_review]);
    }
}
