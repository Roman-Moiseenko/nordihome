<?php

namespace App\Events;

use App\Modules\Accounting\Entity\PricingDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PricingHasCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PricingDocument $pricing;

    public function __construct(PricingDocument $pricing)
    {
        //
        $this->pricing = $pricing;
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
