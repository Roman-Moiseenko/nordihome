<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Lead\Service\LeadService;
use App\Modules\Order\Events\OrderHasCreated;


class LeadNewFromOrder
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasCreated $event): void
    {
        //Письмо клиенту о новом заказе
        $this->service->createLeadFromOrder($event->order);
    }
}
