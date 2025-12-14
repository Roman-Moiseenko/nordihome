<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Entity\LeadStatus;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\OrderHasCanceled;
use App\Modules\Order\Events\OrderHasCreated;
use App\Modules\Order\Events\OrderHasSetManager;

/**
 * Если у заказа поменялся менеджер, меняем его в заявке, и ессли надо, меняем статус заявки
 */
class LeadCanceled
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasCanceled $event): void
    {
        $this->service->canceled($event->order->lead, /*Причина отмены*/);
       // $event->order->lead->setStatus(LeadStatus::STATUS_CANCELED);

        //TODO Причина отмены!!!


    }
}
