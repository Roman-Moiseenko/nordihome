<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\ExpenseHasAssembling;
use App\Modules\Order\Events\ExpenseHasDelivery;
use App\Modules\Order\Events\OrderHasAwaiting;
use App\Modules\Order\Events\OrderHasCanceled;
use App\Modules\Order\Events\OrderHasPaid;

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
