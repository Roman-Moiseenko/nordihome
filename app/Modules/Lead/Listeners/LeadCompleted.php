<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\OrderHasCanceled;
use App\Modules\Order\Events\OrderHasCompleted;

/**
 * Отмена заявки при отмене Заказа по событию OrderHasCanceled
 */
class LeadCompleted
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function handle(OrderHasCompleted $event): void
    {
        $this->service->completed($event->order->lead);
    }
}
