<?php

namespace App\Modules\Mail\Mailable;

use App\Modules\Order\Application\DTOs\OrderViewData;
use App\Modules\Order\Domain\Entities\OrderEntity;
use App\Modules\Order\Infrastructure\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class OrderNew extends SystemMailable
{
    use Queueable, SerializesModels;

    private OrderViewData $order;

    /**
     * Create a new message instance.
     */
    public function __construct(OrderViewData $order)
    {
        parent::__construct();
        $this->order = $order;
        $this->subject = 'Новый заказ';
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.order.new',
            with: [
                'order' => $this->order
            ],
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

    public function getFiles(): array
    {
        return [];
    }

}
