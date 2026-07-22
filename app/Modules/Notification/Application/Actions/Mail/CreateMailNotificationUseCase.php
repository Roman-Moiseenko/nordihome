<?php

namespace App\Modules\Notification\Application\Actions\Mail;

use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;

class CreateMailNotificationUseCase
{

    public function __construct(
        //MAINDO Сервис или репозитории с адресами
        // Сервис отправки
    )
    {

    }
    public function execute(LeadSourceData $leadData)
    {
    }
}
