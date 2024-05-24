<?php

namespace App\Mail;

use App\Modules\Order\Entity\Order\OrderPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentPaid extends Mailable
{
    use Queueable, SerializesModels;

    private OrderPayment $payment;

    /**
     * Create a new message instance.
     */
    public function __construct(OrderPayment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Платеж получен',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.payment.paid',
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
            ->markdown('mail.payment.paid')->with([ 'order' => $this->payment->order, 'payment' => $this->payment]);
    }
}
