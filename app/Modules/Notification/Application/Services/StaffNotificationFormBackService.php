<?php

namespace App\Modules\Notification\Application\Services;

use App\Modules\Mail\Mailable\Inner\FormBackMail;
use App\Modules\Notification\Presentation\Views\Max\MaxFormBackNotification;
use App\Modules\Notification\Presentation\Views\Telegram\TelegramFormBackNotification;
use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Max\Exceptions\CouldNotSendNotification;
use NotificationChannels\Max\MaxMessage;

class StaffNotificationFormBackService
{

    /**
     * @throws CouldNotSendNotification
     */
    public function execute(LeadSourceData $leadData)
    {
        //TODO Получить список контактов а) общий, б) по сотрудникам

        //TODO отправить всем сообщения через Job

        \Log::info('Уведомляем о новой форме !');

        //MAINDO - Отключить перед запуском
        return;

        //Отправляем письмо
        $mail = new FormBackMail($leadData);
        Mail::mailer('system')->to('info@nordihome.ru')->send($mail);

        //Отправляем телеграм

        // Отправка в чат по ID (например, -1001234567890 для группы)
        $chatId = '-1001234567890'; // или просто 123456789

        Notification::route('telegram', $chatId)
            ->notify(new TelegramFormBackNotification($leadData));

        //Отправляем в Max
        $maxChatId = -44555555;
        //Notification::route('max', $maxChatId)->notify(new MaxFormBackNotification($leadData));
        new MaxFormBackNotification($leadData)->toChat($maxChatId);
        //Для сотрудников new MaxFormBackNotification($leadData)->toUser($client->maxChatId);

    }
}
