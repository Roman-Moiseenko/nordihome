<?php

namespace App\Livewire\Admin\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use Livewire\Component;

class UserInfo extends Component
{

    //TODO Добавить в компонент установка адреса и способа платежа(?)
    public Order $order;
    public bool $edit = true;

    public function mount(Order $order, $edit = true)
    {
        $this->order = $order;
        $this->edit = $edit;
    }

    public function render()
    {
        return view('livewire.admin.order.user-info');
    }
}
