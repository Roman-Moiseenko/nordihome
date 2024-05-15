<?php

namespace App\Events;

use App\Modules\Order\Entity\Order\OrderExpense;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpenseHasDelivery
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrderExpense $expense;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderExpense $expense)
    {
        //
        $this->expense = $expense;
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
