<?php

namespace App\Modules\Feedback\Events;

use App\Modules\Feedback\Entity\FormBack;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FormBackHasCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FormBack $form;

    public function __construct(FormBack $form)
    {
        $this->form = $form;
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
