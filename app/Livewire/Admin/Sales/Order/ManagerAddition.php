<?php

namespace App\Livewire\Admin\Sales\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Service\OrderService;
use Livewire\Component;

class ManagerAddition extends Component
{

    public OrderAddition $addition;
    public float $amount;
    public string $comment;
    public int $i;

    private OrderService $service;

    public function boot(OrderService $service)
    {
        $this->service = $service;
    }

    public function mount(OrderAddition $addition, int $i)
    {
        $this->addition = $addition;
        $this->i = $i;

        $this->amount = $addition->amount;
        $this->comment = $addition->comment;
    }

    public function refresh_fields()
    {
        $this->addition->refresh();
        $this->amount = $this->addition->amount;
        $this->comment = $this->addition->comment;
    }

    public function render()
    {
        return view('livewire.admin.sales.order.manager-addition');
    }

    public function set_amount()
    {
        $this->service->addition_amount($this->addition, $this->amount);
        $this->refresh_fields();
        $this->dispatch('update-additions');
        $this->dispatch('update-amount-order');
    }


    public function set_comment()
    {
        $this->service->addition_comment($this->addition, $this->comment);
    }

    public function delete()
    {
        $this->service->addition_delete($this->addition);
        $this->dispatch('update-additions');
        $this->dispatch('update-amount-order');

    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в товаре', message: $e->getMessage());
            $stopPropagation();
        }
    }
}
