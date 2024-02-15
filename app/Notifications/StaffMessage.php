<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class StaffMessage extends Notification implements ShouldQueue
{
    use Queueable;

    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;

    }

    public function via(object $notifiable): array
    {
        return ['telegram', 'database'];
    }

    public function toTelegram(object $notifiable)
    {
        $message = TelegramMessage::create()
            ->content($this->message)
            ->buttonWithCallback('Подтвердить', $notifiable->id);
        //TODO Продумать возврат данных, что сотрудник подтвердил уведомление/заявку
        return $message;
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'staff' => $notifiable->id, // (User::where('telegram_user_id', $notifiable->telegram_user_id)->first())->id,
            'message' => $this->message,
        ];
    }
}
