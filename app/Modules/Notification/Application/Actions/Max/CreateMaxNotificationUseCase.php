<?php

namespace App\Modules\Notification\Application\Actions\Max;

use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;

class CreateMaxNotificationUseCase
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
