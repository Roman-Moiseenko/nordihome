<?php

namespace App\Modules\Lead\Application\Interfaces;

use App\Modules\Lead\Domain\Entities\LeadEntity;

interface LeadRepositoryInterface
{
    public function save(LeadEntity $lead): LeadEntity;
}
