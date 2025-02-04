<?php
declare(strict_types=1);

namespace App\Modules\Mail\Mailable;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Mail\Mailables\Content;
use JetBrains\PhpStorm\Pure;

class ExpenseCompleted extends SystemMailable
{
    private OrderExpense $expense;

    public function __construct(OrderExpense $expense)
    {
        parent::__construct();
        $this->expense = $expense;
    }

    #[Pure] public function content(): Content
    {

        return new Content(
            markdown: 'mail.expense.completed',
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

    public function getName(): string
    {
        return 'Распоряжение выполнено';
    }
}
