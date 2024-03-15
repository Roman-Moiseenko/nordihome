<?php

namespace App\Events;

use App\Modules\Accounting\Entity\DepartureDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DepartureHasCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DepartureDocument $document;

    /**
     * Create a new event instance.
     */
    public function __construct(DepartureDocument $document)
    {
        //
        $this->document = $document;
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
