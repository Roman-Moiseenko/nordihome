<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\ExpenseHasCanceled;
use App\Modules\Order\Events\OrderHasAwaiting;
use App\Modules\Order\Events\OrderHasCanceled;
use App\Modules\Order\Events\OrderHasPaid;
use App\Modules\Order\Events\OrderHasWork;

/**
 * Отмена заявки при отмене Заказа по событию OrderHasCanceled
 */
class LeadReturnPaid
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    public function handle(ExpenseHasCanceled $event): void
    {

        $this->service->returnPaid($event->expense->order->lead);
    }
}
