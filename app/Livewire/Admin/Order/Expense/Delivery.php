<?php

namespace App\Livewire\Admin\Order\Expense;

use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use Livewire\Component;

class Delivery extends Component
{

    public OrderExpense $expense;
    public string $surname;
    public string $firstname;
    public string $secondname;
    public string $comment;
    public string $address;
    public string $phone;
    public ?int $delivery;
    public bool $disabled;


    private ExpenseService $service;

    public function boot(ExpenseService $service)
    {
        $this->service = $service;
    }

    public function mount(OrderExpense $expense, bool $disabled = false)
    {
        $this->expense = $expense;
        $this->refresh_fields();
        $this->disabled = $disabled;
    }

    public function save()
    {
        $expense = $this->expense;
        $expense->recipient->surname = $this->surname;
        $expense->recipient->firstname = $this->firstname;
        $expense->recipient->secondname = $this->secondname;
        $expense->address->address = $this->address;
        $expense->phone = $this->phone;

        //Проверяем данные по умолчанию
        $user = $expense->order->user;
        if (empty($user->fullname->surname)) $user->fullname->surname = $this->surname;
        if (empty($user->fullname->secondname)) $user->fullname->secondname = $this->secondname;
        if (empty($user->address->address)) $user->address->address = $this->address;

        if (!empty($this->delivery)) {
            $expense->type = $this->delivery;
            $user->delivery = $this->delivery;
        }

        $user->save();
        $expense->comment = $this->comment;
        $expense->save();
        $expense->refresh();
    }

    public function refresh_fields()
    {
        $this->surname = $this->expense->recipient->surname;
        $this->firstname = $this->expense->recipient->firstname;
        $this->secondname = $this->expense->recipient->secondname;

        $this->address = $this->expense->address->address;

        $this->comment = $this->expense->comment;
        $this->phone = $this->expense->phone;
        $this->delivery = $this->expense->type;

    }

    public function render()
    {
        return view('livewire.admin.order.expense.delivery');
    }

    public function exception($e, $stopPropagation)
    {
        if ($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Внимание', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
