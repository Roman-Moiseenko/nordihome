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
    private string|null $link_payment;

    public function __construct(Order $order, string|null $invoice, string|null $link_payment)
    {
        parent::__construct();
        $this->subject = 'Заказ подтвержден. Счет на оплату';
        $this->order = $order;
        if (!is_null($invoice)) $this->files['Счет на оплату.xlsx'] = $invoice;
        $this->link_payment = $link_payment;
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.order.awaiting',
            with: [
                'order' => $this->order,
                'link_payment' => $this->link_payment,
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
