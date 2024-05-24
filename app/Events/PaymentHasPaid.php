<?php

namespace App\Events;

use App\Modules\Order\Entity\Order\OrderPayment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentHasPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public OrderPayment $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(OrderPayment $payment)
    {
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
