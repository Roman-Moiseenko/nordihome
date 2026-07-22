<?php

namespace App\Modules\Lead\Traits;

use App\Modules\Lead\Infrastructure\Models\Lead;

/**
 * @property Lead $lead
 */
trait LeadField
{

    public function lead()
    {
        return $this->morphOne(Lead::class, 'leadable')->withDefault();
    }

    public function createLead($data): void
    {
        $this->lead->create_lead($data);
    }
}
