<?php

namespace App\Modules\Lead\Infrastructure\Listeners;

use App\Modules\Lead\Application\Actions\CreateLeadFromFormBackUseCase;
use App\Modules\Shared\Infrastructure\Events\LeadCollected;

readonly class CreateLeadFromFormBack
{
    /**
     * Create the event listener.
     */
    public function __construct(private CreateLeadFromFormBackUseCase $useCase)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(LeadCollected $form): void
    {
        $this->useCase->execute($form->leadData);
    }
}
