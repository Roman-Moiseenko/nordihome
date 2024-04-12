<?php

namespace App\Events;

use App\Modules\Accounting\Entity\SupplyDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SupplyHasCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public SupplyDocument $supply;

    /**
     * Create a new event instance.
     */
    public function __construct(SupplyDocument $supply)
    {
        //
        $this->supply = $supply;
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
