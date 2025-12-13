<?php

namespace App\Modules\Lead\Traits;

use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;

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
