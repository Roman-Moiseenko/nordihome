<?php
declare(strict_types=1);

namespace App\Modules\Notification\Message;

use Illuminate\Notifications\Notification;
use NotificationChannels\SmscRu\SmscRuChannel;
use NotificationChannels\SmscRu\SmscRuMessage;

class UserSMSMessage extends Notification
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        //TODO Google_Calendar, VK, SMS
        return [SmscRuChannel::class];
    }

    public function toSmscRu($notifiable)
    {
        return SmscRuMessage::create($this->message);
    }

}
