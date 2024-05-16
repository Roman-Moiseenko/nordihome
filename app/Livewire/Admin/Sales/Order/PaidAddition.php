<?php

namespace App\Livewire\Admin\Sales\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use Livewire\Component;

class PaidAddition extends Component
{

    public OrderAddition $addition;
    public int $i;
    public Order $order;

    public int $amount;
    public bool $enabled = true;
    public function boot()
    {


    }

    public function mount(OrderAddition $addition, int $i)
    {
        $this->order = $addition->order;
        $this->addition = $addition;
        $this->i = $i;
        $this->refresh_fields();

    }

    public function refresh_fields()
    {
        $this->addition->refresh();
        $this->amount = $this->addition->getRemains();
    }

    public function set_amount()
    {
        $this->dispatch('issuance-update', addition_id: $this->addition->id, amount: $this->amount, enabled: $this->enabled);
    }

    public function toggle_enabled()
    {
        //$this->refresh_fields();
        $this->dispatch('issuance-update', addition_id: $this->addition->id, amount: $this->amount, enabled: $this->enabled);
    }

    public function render()
    {
        return view('livewire.admin.sales.order.paid-addition');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в товаре', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
