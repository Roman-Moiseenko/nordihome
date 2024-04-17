<?php

namespace App\Mail;

use App\Modules\Product\Entity\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductNew extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Product[]
     */
    private array $products;

    /**
     * @param Product[] $products
     */
    public function __construct(array $products)
    {
        //
        $this->products = $products;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Поступление нового товара',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.subscription.product-new',
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
        return $this->subject('Поступление нового товара')
            ->markdown('mail.subscription.product-new')->with([ 'products' => $this->products]);
    }
}
