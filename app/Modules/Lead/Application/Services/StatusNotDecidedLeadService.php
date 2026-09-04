<?php

namespace App\Modules\Lead\Application\Services;

use App\Modules\Lead\Application\Actions\SetStatusLeadUseCase;
use App\Modules\Lead\Domain\ValueObjects\LeadStatusValue;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

readonly class StatusNotDecidedLeadService
{
    public function __construct(
        private SetStatusLeadUseCase        $statusLeadUseCase,
    )
    {}

    public function execute(int $leadId, int $staffId, UserPermission $permission): void
    {
        if (!$permission->can('lead.lead.edit')) throw new AccessDeniedException();
        //Меняем только статус Лида
        $this->statusLeadUseCase->execute($leadId, LeadStatusValue::NOT_DECIDED);
        //Возможно какая-то аналитика / уведомления


    }
}
