<?php

namespace App\Livewire\Admin\Sales\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Service\OrderReserveService;
use Livewire\Component;

class PaidItem extends Component
{
    public Order $order;
    public OrderItem $item;
    public int $i;

    public array $storages;

    public int $quantity;
    public bool $enabled = true;

    private OrderReserveService $reserveService;

    public function boot(OrderReserveService $reserveService)
    {
        $this->reserveService = $reserveService;
    }

    public function mount(OrderItem $item, int $i, $storages)
    {
        $this->order = $item->order;
        $this->item = $item;
        $this->i = $i;
        $this->storages = $storages;
        $this->refresh_fields();
    }

    public function refresh_fields()
    {
        $this->item->refresh();
        $this->quantity = $this->item->getRemains();

    }

    public function set_quantity()
    {
        $this->dispatch('issuance-update', item_id: $this->item->id, quantity: $this->quantity, enabled: $this->enabled);
    }

    public function toggle_enabled()
    {
        //$this->refresh_fields();
        $this->dispatch('issuance-update', item_id: $this->item->id, quantity: $this->quantity, enabled: $this->enabled);
    }

    public function reserve_up($storage, $quantity)
    {
        $this->reserveService->CollectReserve($this->item, $storage, $quantity);
        $this->refresh_fields();
    }

    public function render()
    {
        return view('livewire.admin.sales.order.paid-item');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в товаре', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
