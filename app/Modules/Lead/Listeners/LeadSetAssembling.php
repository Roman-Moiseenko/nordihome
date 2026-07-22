<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\ExpenseHasAssembling;

/**
 * Отмена заявки при отмене Заказа по событию OrderHasCanceled
 */
class LeadSetAssembling
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function handle(ExpenseHasAssembling $event): void
    {
        $this->service->assembly($event->expense->order->lead);
    }
}
