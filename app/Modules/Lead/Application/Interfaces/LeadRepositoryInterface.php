<?php

namespace App\Modules\Lead\Application\Interfaces;

use App\Modules\Lead\Domain\Entities\LeadEntity;

interface LeadRepositoryInterface
{
    public function save(LeadEntity $lead): LeadEntity;

    public function findByOrderId(int $orderId):? LeadEntity;

    public function findById(int $id): ?LeadEntity;

    /**
     * @param string $status Значение статуса из LeadStatusValue
     * @param int|null $staff_id Если null — отбор по сотруднику не проводится
     * @return LeadEntity[]
     */
    public function findByStatus(string $status, ?int $staff_id = null): array;
}
