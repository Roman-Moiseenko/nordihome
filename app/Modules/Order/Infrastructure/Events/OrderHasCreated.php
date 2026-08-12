<?php

namespace App\Modules\Order\Infrastructure\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderHasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $orderId;
    private string $action;

    /**
     * Слушатели - уведомления, доставка и платежи (сервисы)
     */
    public function __construct(int $orderId, string $action = '')
    {
        $this->orderId = $orderId;
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
