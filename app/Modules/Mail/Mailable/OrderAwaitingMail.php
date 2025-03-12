<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\Order\Entity\Order\Order;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class OrderAwaitingMail extends SystemMailable
{

    private Order $order;

    public function __construct(Order $order, string $invoice)
    {
        parent::__construct();
        $this->subject = 'Заказ подтвержден. Счет на оплату';
        $this->order = $order;
        $this->files['Счет на оплату.xlsx'] = $invoice;
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.order.awaiting',
            with: [
                'order' => $this->order
            ],
        );
    }

    public function attachments(): array
    {
        return array_map(function ($item) {
            return Attachment::fromPath($item);
        }, $this->files);
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getName(): string
    {
        return 'Письмо на оплату';
    }
}
