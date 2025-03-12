<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class WriteReview extends SystemMailable
{
    private OrderExpense $expense;

    public function __construct(OrderExpense $expense)
    {
        parent::__construct();
        $this->expense = $expense;
        $this->subject = 'Напишите отзыв';
    }

    #[Pure] public function content(): Content
    {
        return new Content(
            markdown: 'mail.review.write',
            with: [
                'expense' => $this->expense,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function getFiles(): array
    {
        return [];
    }

}
