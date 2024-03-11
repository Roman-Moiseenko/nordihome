<?php

namespace App\Events;

use App\Modules\Order\Entity\Payment\PaymentOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentHasPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PaymentOrder $payment;

    /**
     * Create a new event instance.
     */
    public function __construct(PaymentOrder $payment)
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
