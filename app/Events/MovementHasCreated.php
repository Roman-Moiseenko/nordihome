<?php

namespace App\Events;

use App\Modules\Accounting\Entity\MovementDocument;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MovementHasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public MovementDocument $document;

    public function __construct(MovementDocument $document)
    {
        $this->document = $document;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
