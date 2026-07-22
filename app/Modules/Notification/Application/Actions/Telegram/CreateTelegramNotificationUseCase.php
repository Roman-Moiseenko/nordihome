<?php

namespace App\Modules\Notification\Application\Actions\Telegram;

use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;

class CreateTelegramNotificationUseCase
{
    public function __construct(
        //MAINDO Сервис или репозитории с адресами
        // Сервис отправки
    )
    {}

    public function execute(LeadSourceData $leadData)
    {
    }
}
