<?php
declare(strict_types=1);

namespace App\Modules\Notification\Message;

use App\Modules\Notification\Helpers\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use JetBrains\PhpStorm\ExpectedValues;
use NotificationChannels\SmscRu\SmscRuChannel;

class UserMessage extends Notification implements ShouldQueue
{
    use Queueable;

    protected int $event;
    protected string $message;

    public function __construct(
        #[ExpectedValues(valuesFromClass: NotificationHelper::class)] int $event,
        string $message
    )
    {
        $this->event = $event;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {

        //TODO Google_Calendar, VK, SMS
        // либо в настройках клиента (каналы уведомления) либо отдельными классами
        return [];
    }
}
