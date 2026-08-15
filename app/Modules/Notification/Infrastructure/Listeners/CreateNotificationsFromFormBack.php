<?php

namespace App\Modules\Notification\Infrastructure\Listeners;

use App\Modules\Mail\Entity\MailTemplateRegistry;
use App\Modules\Notification\Application\Actions\Max\CreateMaxNotificationUseCase;
use App\Modules\Notification\Application\Actions\Telegram\CreateTelegramNotificationUseCase;
use App\Modules\Shared\Application\Interfaces\Mail\MailServiceInterface;
use App\Modules\Shared\Domain\Entities\Mail\Recipient;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;
use NotificationChannels\Max\Exceptions\CouldNotSendNotification;

readonly class CreateNotificationsFromFormBack
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private MailServiceInterface $mailService,
        private CreateMaxNotificationUseCase $maxNotificationUseCase,
        private CreateTelegramNotificationUseCase $telegramNotificationUseCase,
    )
    {
    }

    /**
     * Handle the event.
     * @throws CouldNotSendNotification
     */
    public function handle(LeadCollected $form): void
    {

        $leadData = $form->leadData;

        //Готовим письмо
        if (is_null($leadData->orderId)) {
            $template = MailTemplateRegistry::get('lead.form');
        } else {
            $template = MailTemplateRegistry::get('lead.order');
        }

        $this->mailService->send(
            $template,
            [
                'data' => $leadData->data,
                'orderId' => $leadData->orderId,
            ],
            new Recipient(email: config('mail.notification.address'), clientId: null)
        );
        return;

        //Уведомления в рабочие чаты
        if (config('app.env') == 'production') {
            $this->maxNotificationUseCase->execute($leadData);
            $this->telegramNotificationUseCase->execute($leadData);
        }
    }
}
