<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\OrderHasAwaiting;

/**
 * Отмена заявки при отмене Заказа по событию OrderHasCanceled
 */
class LeadSetAwaiting
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function handle(OrderHasAwaiting $event): void
    {
        $this->service->awaiting($event->order->lead);
    }
}
