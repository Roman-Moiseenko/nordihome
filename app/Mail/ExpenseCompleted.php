<?php

namespace App\Mail;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpenseCompleted extends Mailable
{
    use Queueable, SerializesModels;

    private OrderExpense $expense;

    /**
     * Create a new message instance.
     */
    public function __construct(OrderExpense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Товар получен',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.expense.completed',
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
            ->markdown('mail.expense.completed')->with([ 'expense' => $this->expense, 'order' => $this->expense->order]);
    }
}
