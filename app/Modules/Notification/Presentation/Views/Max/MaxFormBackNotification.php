<?php

namespace App\Modules\Notification\Presentation\Views\Max;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use Illuminate\Notifications\Notification;
use NotificationChannels\Max\Exceptions\CouldNotSendNotification;
use NotificationChannels\Max\MaxChannel;
use NotificationChannels\Max\MaxMessage;

class MaxFormBackNotification extends Notification
{
    protected $message;

    public function __construct(LeadSourceData $data)
    {
        $form = $data->data['form'];
        unset($data->data['form']);
        unset($data->data['agreement']);
        $message = '<p><b>' . $form . '</b></p>';
        foreach ($data->data as $key => $value)
            $message .= '<p>' . $key . ': ' . $value . '</p>';

        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return [MaxChannel::class];
    }

    public function toMax($notifiable): MaxMessage
    {
        return MaxMessage::create($this->message)
            ->html();
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function toChat(int $chatId): array
    {
        return $this->toMax(null)->toChat($chatId)->send();
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function toUser(int $userId): array
    {
        return $this->toMax(null)->to($userId)->send();
    }
}
