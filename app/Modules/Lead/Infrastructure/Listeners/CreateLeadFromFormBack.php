<?php

namespace App\Modules\Lead\Infrastructure\Listeners;

use App\Modules\Lead\Application\Actions\CreateLeadFromFormBackUseCase;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;

class CreateLeadFromFormBack
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly CreateLeadFromFormBackUseCase $useCase)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LeadCollected $form): void
    {
        $this->useCase->execute($form->leadData);
    }
}
