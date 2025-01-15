<?php

namespace App\Modules\Notification\Events;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Employee\Entity\Employee;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class TelegramHasReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Admin|Worker $user;
    public int $operation;
    public int $id;

    public function __construct(Admin|Worker $user, int $operation, int $id)
    {
        $this->user = $user;
        $this->operation = $operation;
        $this->id = $id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
