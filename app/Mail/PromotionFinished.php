<?php

namespace App\Mail;

use App\Modules\Discount\Entity\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromotionFinished extends Mailable
{
    use Queueable, SerializesModels;

    private Promotion $promotion;

    /**
     * Create a new message instance.
     */
    public function __construct(Promotion $promotion)
    {
        //
        $this->promotion = $promotion;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Promotion Finished',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.promotion.finished',
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
        return $this->subject('Акция закончена')
            ->markdown('mail.promotion.finished')->with([ 'promotion' => $this->promotion]);
    }
}
