<?php

namespace App\Mail;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Catalog\Infrastructure\Models\Product;
use Illuminate\Bus\Queueable;
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
    private Client $client;

    /**
     * @param Product[] $products
     */
    public function __construct(array $products, Client $client)
    {
        //
        $this->products = $products;
        $this->client = $client;
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
            markdown: 'mail.subscription.product-arrival',
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
            ->markdown('mail.subscription.product-arrival')->with([ 'client' => $this->client, 'products' => $this->products]);
    }
}
