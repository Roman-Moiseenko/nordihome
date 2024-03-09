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

    /** @var MovementDocument[] $documents  */
    public array $documents;

    public function __construct(array $documents)
    {
        $this->documents = $documents;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
