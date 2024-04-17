<?php

namespace App\Mail;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductArrival extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Product[]
     */
    private array $products;
    private User $user;

    /**
     * @param Product[] $products
     */
    public function __construct(array $products, User $user)
    {
        //
        $this->products = $products;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Поступление товара из избранного',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user.arrival',
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
        return $this->subject('Поступление товара из избранного')
            ->markdown('mail.user.arrival')->with([ 'user' => $this->user, 'products' => $this->products]);
    }
}
