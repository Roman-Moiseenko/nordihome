<?php

namespace App\Modules\Notification\Application\Actions\Telegram;

use App\Modules\Notification\Presentation\Views\Telegram\TelegramFormBackNotification;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class CreateTelegramNotificationUseCase
{
    public function __construct(private Settings $settings
        //TODO Сервис или репозитории с адресами
        // Сервис отправки
    )
    {}

    public function execute(LeadSourceData $leadData)
    {
        //$chatId = '-1001234567890'; // или просто 123456789

        $chatId = config('shop.telegram-chat-id');

        if (is_null($leadData->orderId)) {
            $form = $leadData->data['form'];
            unset($leadData->data['form']);
            unset($leadData->data['agreement']);
        } else {
            $form = 'Заказ на сайте ';
        }

        $message = $form . '\n\r';
        foreach ($leadData->data as $key => $value)
            $message .= $key . ': ' . $value . '\n\r';

        $tg =TelegramMessage::create()->content($message);
        if (!is_null($leadData->orderId)) {
            $url = route('admin.order.edit', $leadData->orderId);
            $tg->button('Перейти', $url, 1);
        }

        $tg->to($chatId)->send();

/*
        Notification::route('telegram', $chatId)
            ->notify(new TelegramFormBackNotification($leadData));
        */
    }
}
