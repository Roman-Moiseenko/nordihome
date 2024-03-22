<?php

namespace App\Events;

use App\Modules\Order\Entity\Order\OrderAddition;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentHasPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrderAddition $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderAddition $payment)
    {
        //
        $this->payment = $payment;
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
