<?php

namespace App\Livewire\Admin\Sales\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Service\OrderService;
use Livewire\Component;
use Livewire\Attributes\On;

class ManagerItems extends Component
{

    private OrderService $service;
    public Order $order;
    public float $amount;

    public int $form_product = -1;
    public float $form_quantity = 1;


    public function boot(OrderService $service)
    {
        $this->service = $service;
    }

    public function mount(Order $order)
    {
        $this->order = $order;
        $this->refresh_fields();
    }
    #[On('update-items')]
    public function refresh_fields()
    {
        $this->order->refresh();
    }

    #[On('delete-item')]
    public function del_item($item_id)
    {
        $this->service->delete_item(OrderItem::find($item_id));
        $this->refresh_fields();
        $this->dispatch('update-amount-order');
    }

    #[On('add-product')]
    public function add_item($product_id, $quantity)
    {
        if (!is_numeric($product_id)) throw new \DomainException('Что-то пошло не так, обновите страницу');
        $this->service->add_product($this->order, (int)$product_id, (int)$quantity);
        $this->refresh_fields();
        $this->dispatch('update-amount-order');
    }

    public function render()
    {
        return view('livewire.admin.sales.order.manager-items');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в товаре', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }


}
