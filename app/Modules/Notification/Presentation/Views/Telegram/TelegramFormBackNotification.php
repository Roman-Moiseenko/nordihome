<?php

namespace App\Modules\Notification\Presentation\Views\Telegram;

use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
class TelegramFormBackNotification extends Notification
{
    use Queueable;

    protected $message;

    /**
     * Создание экземпляра уведомления.
     *
     * @param string $message
     */
    public function __construct(LeadSourceData $data)
    {
        $form = $data->data['form'];
        unset($data->data['form']);
        unset($data->data['agreement']);
        $message = $form . '\n\r';
        foreach ($data->data as $key => $value)
            $message .= $key . ': ' . $value . '\n\r';

        $this->message = $message;
    }

    /**
     * Каналы доставки.
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    /**
     * Формирование сообщения для Telegram.
     */
    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            ->content($this->message);
            // Опционально: добавить кнопку
            // ->button('Посмотреть', 'https://example.com')
            // Указать чат (если не передан через маршрут)
            // ->chat($notifiable->telegram_chat_id);
    }
}
