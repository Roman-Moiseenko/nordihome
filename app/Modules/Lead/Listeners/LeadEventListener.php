<?php

namespace App\Modules\Lead\Listeners;

use App\Modules\Feedback\Events\FormBackHasCreated;
use App\Modules\Lead\Service\LeadService;

class LeadEventListener
{
    private LeadService $service;

    public function __construct(LeadService $service)
    {

        $this->service = $service;
    }

    public function onFormBack(FormBackHasCreated $event): void
    {
        $this->service->createLeadFromForm($event->form);
    }

    public function subscribe($events): void
    {
        //Лид от Формы обратной связи
        $events->listen(
            'App\Modules\Feedback\Events\FormBackHasCreated',
            'App\Modules\Lead\Listeners\LeadEventListener@onFormBack'
        );

        //Лид от Авито


        //Лид от Бота телеграм


        //Лид от VK

        //Лид от Чат бота

      /*  $events->listen(
            'App\Modules\Feedback\Events\.....',
            'App\Modules\Lead\Listeners\LeadEventListener@on....'
        );
        */
    }
}
