<?php

namespace App\Modules\Notification\Events;

use App\Modules\Auth\Infrastructure\Models\Staff;
use App\Modules\Employee\Entity\Employee;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class TelegramHasReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Staff $staff;
    public int $operation;
    public int $id;

    public function __construct(Staff $staff, int $operation, int $id)
    {
        $this->staff = $staff;
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
