<?php

namespace App\Events;

use App\Modules\Shop\Parser\ProductParser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParserPriceHasChange
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProductParser $productParser;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductParser $productParser)
    {
        $this->productParser = $productParser;
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
