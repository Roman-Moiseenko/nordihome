<?php

namespace App\Livewire\Admin\Sales\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderAddition;
use App\Modules\Order\Service\OrderService;
use Livewire\Attributes\On;
use Livewire\Component;

class ManagerAdditions extends Component
{

    private OrderService $service;
    public Order $order;
    public float $amount;

    public int $form_purpose;
    public float $form_amount = 0;
    public string $form_comment = '';

    public function boot(OrderService $service)
    {
        $this->service = $service;
    }

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->refresh_fields();
    }


    #[On('update-additions')]
    public function refresh_fields()
    {
        $this->order->refresh();
        $this->amount = $this->order->getAdditionsAmount();
    }

    public function add_addition()
    {
        $this->service->add_addition($this->order,
            $this->form_purpose, $this->form_amount, $this->form_comment);
        $this->form_purpose = 0;
        $this->form_amount = 0;
        $this->form_comment = '';
        $this->refresh_fields();
        $this->dispatch('update-amount-order');
    }

    public function render()
    {
        return view('livewire.admin.sales.order.manager-additions');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в Услугах', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
