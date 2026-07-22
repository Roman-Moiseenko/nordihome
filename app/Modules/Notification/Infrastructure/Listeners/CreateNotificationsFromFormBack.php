<?php

namespace App\Modules\Notification\Infrastructure\Listeners;

use App\Modules\Lead\Application\Actions\CreateLeadFromFormBackUseCase;
use App\Modules\Notification\Application\Actions\Mail\CreateMailNotificationUseCase;
use App\Modules\Notification\Application\Actions\Max\CreateMaxNotificationUseCase;
use App\Modules\Notification\Application\Actions\Telegram\CreateTelegramNotificationUseCase;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;

class CreateNotificationsFromFormBack
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly CreateMailNotificationUseCase $createMailNotificationUseCase,
        private readonly CreateTelegramNotificationUseCase $createTelegramNotificationUseCase,
        private readonly CreateMaxNotificationUseCase $createMaxNotificationUseCase,
    )
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LeadCollected $form): void
    {
        $this->createMailNotificationUseCase->execute($form->leadData);
        $this->createTelegramNotificationUseCase->execute($form->leadData);
        $this->createMaxNotificationUseCase->execute($form->leadData);
    }
}
