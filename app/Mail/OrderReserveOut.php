<?php

namespace App\Mail;

use App\Modules\Order\Entity\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReserveOut extends Mailable
{
    use Queueable, SerializesModels;

    private Order $order;
    private bool $timeOut;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, bool $timeOut)
    {
        $this->order = $order;
        $this->timeOut = $timeOut;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->timeOut) ? 'Резерв закончился' : 'Резерв заканчивается',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.order.reserve-out',
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
            ->markdown('mail.order.reserve-out')->with([ 'order' => $this->order, 'timeOut' => $this->timeOut]);
    }
}
