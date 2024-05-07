<?php

namespace App\Livewire\Admin\Sales\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Service\OrderService;
use Livewire\Attributes\On;
use Livewire\Component;

class ManagerAmount extends Component
{
    public Order $order;
    public int $manual;
    public $percent;
    public $coupon;


    private OrderService $service;

    public function boot(OrderService $service)
    {
        $this->service = $service;

    }

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->refresh_fields();
    }

    #[On('update-amount-order')]
    public function refresh_fields()
    {
        $this->order->refresh();
        $this->coupon = is_null($this->order->coupon_id) ? '' : $this->order->coupon->code;
        $this->manual = $this->order->manual;
        $this->percent = ($this->order->getBaseAmountNotDiscount() == 0)
            ? 0
            : number_format($this->order->manual / $this->order->getBaseAmountNotDiscount() * 100, 2, '.');

    }
    public function updated($property, $value)
    {
        if ($property == 'percent') {
            if (!is_numeric($value)) throw new \DomainException('Должно быть число');
        }
    }

    public function updating($property, $value)
    {

        if ($property == 'percent') {
            if (!is_numeric($value)) throw new \DomainException('Должно быть число');
        }
    }

    public function set_coupon()
    {
        $this->service->set_coupon($this->order, $this->coupon);
        $this->refresh_fields();
    }

    public function set_manual()
    {
        //if ($this->manual != 0 && empty($this->manual)) $this->manual = 0;
        $this->service->discount_order($this->order, $this->manual);
        $this->refresh_fields();
        $this->dispatch('update-item');
    }

    public function set_percent()
    {

        $this->service->discount_order_percent($this->order, $this->percent);
        $this->refresh_fields();
        $this->dispatch('update-item');
    }

    public function render()
    {
        return view('livewire.admin.sales.order.manager-amount');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Неверные параметры', message: $e->getMessage());
            $stopPropagation();
        }
    }
}
