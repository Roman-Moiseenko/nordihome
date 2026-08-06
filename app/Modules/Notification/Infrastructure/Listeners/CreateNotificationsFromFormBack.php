<?php

namespace App\Modules\Notification\Infrastructure\Listeners;

use App\Modules\Notification\Application\Services\StaffNotificationFormBackService;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use NotificationChannels\Max\Exceptions\CouldNotSendNotification;

readonly class CreateNotificationsFromFormBack
{
    /**
     * Create the event listener.
     */
    public function __construct(
//private readonly CreateMailNotificationUseCase $createMailNotificationUseCase,
     //   private readonly CreateTelegramNotificationUseCase $createTelegramNotificationUseCase,
     //   private readonly CreateMaxNotificationUseCase $createMaxNotificationUseCase,
        private StaffNotificationFormBackService $staffNotificationFormBackService,
    )
    {
        //
    }

    /**
     * Handle the event.
     * @throws CouldNotSendNotification
     */
    public function handle(LeadCollected $form): void
    {

        $this->staffNotificationFormBackService->execute($form->leadData);
    //    $this->createTelegramNotificationUseCase->execute($form->leadData);
//        $this->createMaxNotificationUseCase->execute($form->leadData);
    }
}
