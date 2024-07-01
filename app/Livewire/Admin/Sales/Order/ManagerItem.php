<?php

namespace App\Livewire\Admin\Sales\Order;

use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderItem;
use App\Modules\Order\Service\OrderService;
use Livewire\Attributes\On;
use Livewire\Component;

class ManagerItem extends Component
{
    public OrderItem $item;

    public int $i;
    public bool $edit;
    public string $comment;


    private OrderService $service;
    public float $sell_percent;
    public float $sell_cost;
    public bool $assemblage;
    public int $quantity;

    public function boot(OrderService $service)
    {
        $this->service = $service;
    }

    public function mount(OrderItem $item, int $i, bool $edit)
    {
        $this->item = $item;

        $this->i = $i;
        $this->edit = $edit;

        $this->refresh_fields();
    }

    public function updating($property, $value)
    {
        if ($property === 'sell_percent') {
            if (!is_numeric($value)) throw new \DomainException('Должно быть число');
        }
    }

    #[On('update-item')]
    public function refresh_fields()
    {
        $this->item->refresh();
        $this->sell_cost = $this->item->sell_cost;
        $this->sell_percent = number_format(($this->item->base_cost - $this->item->sell_cost) / $this->item->base_cost * 100, 2);
        $this->comment = $this->item->comment;
        $this->assemblage = $this->item->assemblage;
        $this->quantity = $this->item->quantity;
    }

    public function render()
    {
        return view('livewire.admin.sales.order.manager-item');
    }

    public function set_sell()
    {
        $this->service->update_sell($this->item, (int)$this->sell_cost);
        $this->refresh_fields();
        $this->dispatch('update-amount-order');
    }

    public function set_quantity()
    {
        $this->service->update_quantity($this->item, $this->quantity);
        $this->refresh_fields();
        $this->dispatch('update-amount-order');
    }
    public function set_percent()
    {
        $this->service->discount_item_percent($this->item, (float)$this->sell_percent);
        $this->refresh_fields();
        $this->dispatch('update-amount-order');
    }

    public function check_assemblage()
    {
        $this->service->check_assemblage($this->item);
        $this->dispatch('update-amount-order');
    }

    public function set_comment()
    {
        $this->service->update_item_comment($this->item, $this->comment);
    }

    public function delete()
    {
        //Отправляем в компонент Элементов, чтоб синхронизировать показ
        //$order = $this->item->order;
        //$this->service->delete_item($this->item);
        //$this->dispatch('update-items');
        $this->dispatch('delete-item', item_id: $this->item->id);
//        $this->redirect(route('admin.sales.order.show', $order));
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в товаре', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
