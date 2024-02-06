<?php

namespace App\Events;

use App\Modules\Accounting\Entity\ArrivalDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArrivalHasCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ArrivalDocument $arrival;

    /**
     * Create a new event instance.
     */
    public function __construct(ArrivalDocument $arrival)
    {
        //
        $this->arrival = $arrival;
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
