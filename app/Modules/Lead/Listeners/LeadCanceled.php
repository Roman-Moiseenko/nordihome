<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Infrastructure\Models\Lead;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\OrderHasCanceled;

/**
 * Отмена заявки при отмене Заказа по событию OrderHasCanceled
 */
class LeadCanceled
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function handle(OrderHasCanceled $event): void
    {
        $this->service->canceled($event->order->lead, Lead::CANCELED_ORDER_MANAGER);
    }
}
