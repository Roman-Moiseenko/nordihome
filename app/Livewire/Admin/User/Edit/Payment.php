<?php

namespace App\Livewire\Admin\User\Edit;

use App\Modules\User\Entity\User;
use Livewire\Component;

class Payment extends Component
{
    public User $user;
    public bool $edit = true;
    public string $payment;

    public bool $change = false;
    //TODO Сделать, после ТЗ по Оплате

    public string $html_payment;

    public function mount(User $user, $edit = true)
    {
        $this->user = $user;
        $this->edit = $edit;

        $this->html_payment = $user->htmlPayment();
    }

    public function open_change()
    {

    }

    public function render()
    {
        return view('livewire.admin.user.edit.payment');
    }
}
