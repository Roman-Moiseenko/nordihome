<?php

namespace App\Modules\Notification\Application\Actions\Max;

use App\Modules\Notification\Presentation\Views\Max\MaxFormBackNotification;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use NotificationChannels\Max\MaxMessage;

class CreateMaxNotificationUseCase
{
    public function __construct(
        //MAINDO Сервис или репозитории с адресами
        // Сервис отправки
    )
    {}

    public function execute(LeadSourceData $leadData)
    {

        if (!is_null($leadData->orderId)) {
            $form = $leadData->data['form'];
            unset($leadData->data['form']);
            unset($leadData->data['agreement']);
        } else {
            $form = 'Заказ на сайте ' . '<a href="' . route('admin.order.edit', $leadData->orderId) . '">Перейти</a>';
        }
        $message = '<p><b>' . $form . '</b></p>';
        foreach ($leadData->data as $key => $value)
            $message .= '<p>' . $key . ': ' . $value . '</p>';

        $maxChatId = config('shop.max-chat-id');

        MaxMessage::create($message)->html()->toChat($maxChatId)->send();
    }

}
