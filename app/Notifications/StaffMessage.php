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
    private string $route;
    private string $title;
    private string $icon;

    public function __construct(string $title, string $message, string $route = '', string $icon = '')
    {
        $this->message = $message;

        $this->route = $route;
        $this->title = $title;
        $this->icon = $icon;
    }

    public function via(object $notifiable): array
    {
        if (app()->environment() === 'production' && $notifiable->telegram_user_id > 0) return ['telegram', 'database'];
        return ['database'];
    }

    public function toTelegram(object $notifiable)
    {
        $message = TelegramMessage::create()->content($this->title)->line($this->message);

        //TODO Продумать возврат данных, что сотрудник подтвердил уведомление/заявку
        // ->buttonWithCallback('Подтвердить', $notifiable->id);
        if (!empty($this->route)) $message = $message->button('Перейти', $this->route);
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
            'title' => $this->title,
            'message' => $this->message,
            'route' => $this->route,
            'icon' => $this->icon,
        ];
    }
}
