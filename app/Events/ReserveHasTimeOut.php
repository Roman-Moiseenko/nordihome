<?php

namespace App\Events;

use App\Modules\Order\Entity\Order\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReserveHasTimeOut
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public bool $timeOut;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, bool $timeOut = true)
    {
        //
        $this->order = $order;
        $this->timeOut = $timeOut;
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
