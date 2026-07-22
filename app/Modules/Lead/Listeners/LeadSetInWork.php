<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\OrderHasWork;

/**
 * Отмена заявки при отмене Заказа по событию OrderHasCanceled
 */
class LeadSetInWork
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function handle(OrderHasWork $event): void
    {
        $this->service->work($event->order->lead);
    }
}
