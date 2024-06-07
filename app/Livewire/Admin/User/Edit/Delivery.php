<?php

namespace App\Livewire\Admin\User\Edit;

use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\User\Entity\User;
use Livewire\Component;

class Delivery extends Component
{
    public User $user;
    public bool $edit;

    public bool $change = false;
    public string $address = '';
    public int $delivery;

    public string $html_delivery;

    public function mount(User $user, $edit = true)
    {
        $this->user = $user;
        $this->edit = $edit;
        $this->address = $user->address->address;
        $this->delivery = $user->delivery ?? OrderExpense::DELIVERY_STORAGE;
        $this->html_delivery = $user->htmlDelivery();
    }


    public function open_change()
    {
        $this->change = true;
    }

    public function save_change()
    {
        $this->user->address->address = $this->address;
        $this->user->delivery = $this->delivery;
        $this->user->save();
        $this->user->refresh();
        $this->html_delivery = $this->user->htmlDelivery();
        $this->change = false;
    }

    public function close_change()
    {
        $this->address = $this->user->address->address;
        $this->delivery = $this->user->delivery ?? OrderExpense::DELIVERY_STORAGE;
        $this->html_delivery = $this->user->htmlDelivery();
        $this->change = false;
    }

    public function render()
    {
        return view('livewire.admin.user.edit.delivery');
    }
}
