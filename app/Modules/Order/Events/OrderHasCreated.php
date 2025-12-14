<?php

namespace App\Modules\Order\Events;

use App\Modules\Order\Entity\Order\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

//TODO Перенести в модуль Order

class OrderHasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    private string $action;

    /**
     * Слушатели - уведомления, доставка и платежи (сервисы)
     */
    public function __construct(Order $order, string $action = '')
    {
        $this->order = $order;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
