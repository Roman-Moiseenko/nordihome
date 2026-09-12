<?php

namespace App\Modules\Shared\Infrastructure\Events;

use App\Modules\Shared\Application\DTOs\Lead\LeadSourceData;

readonly class LeadCollected extends CustomEvent
{
    public function __construct(
        public LeadSourceData $leadData
    ) {}
}
