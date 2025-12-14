<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Entity\LeadStatus;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\OrderHasCreated;
use App\Modules\Order\Events\OrderHasSetManager;

/**
 * Если у заказа поменялся менеджер, меняем его в заявке, и ессли надо, меняем статус заявки
 */
class LeadSetManager
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasSetManager $event): void
    {
        $lead = $event->order->lead;

        if ($lead->staff_id == $event->order->staff_id) return;

        $lead->staff_id = $event->order->staff_id;
        $lead->save();
        if ($lead->isNew()) {
            $lead->setStatus(LeadStatus::STATUS_IN_WORK);
        }

    }
}
