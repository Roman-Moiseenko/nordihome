<?php

namespace App\Livewire\Admin\Order\Manager;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Service\OrderService;
use Livewire\Attributes\On;
use Livewire\Component;

class Info extends Component
{


    public Order $order;

    public string $comment;
    public string $volume;
    public string $weight;
    public bool $edit;
    public bool $show = false;

    private OrderService $service;

    public function boot(OrderService $service)
    {
        $this->service = $service;
    }

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->comment = $order->comment;
        if ($order->isCanceled() || $order->isCompleted()) {
            $this->edit = false;
        } else {
            $this->edit = true;
        }

        //$this->refresh_fields();
    }
    #[On('update-amount-order')]
    public function refresh_fields()
    {
        $this->order->refresh();

    /*    $this->weight = $this->order->getWeight();
        $this->volume = $this->order->getVolume();
        $this->comment = $this->order->comment;*/
    }

    public function toggle_fields()
    {
        $this->show = !$this->show;
    }

    public function set_comment()
    {
        $this->service->update_comment($this->order, $this->comment);
    }

    public function render()
    {
        return view('livewire.admin.order.manager.info');
    }
}
