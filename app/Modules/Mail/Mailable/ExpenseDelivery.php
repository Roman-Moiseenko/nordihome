<?php

namespace App\Modules\Mail\Mailable;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class ExpenseDelivery extends SystemMailable
{
    private OrderExpense $expense;

    public function __construct(OrderExpense $expense)
    {
        parent::__construct();
        $this->expense = $expense;
        $this->subject = 'Отгрузке присвоен трек-номер';
    }

    #[Pure] public function content(): Content
    {

        return new Content(
            markdown: 'mail.expense.delivery',
            with: [
                'expense' => $this->expense,
                'order' => $this->expense->order,
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
