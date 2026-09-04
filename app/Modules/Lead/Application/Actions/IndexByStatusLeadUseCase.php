<?php

namespace App\Modules\Lead\Application\Actions;

use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Lead\Application\Interfaces\LeadRepositoryInterface;
use App\Modules\Lead\Domain\Entities\LeadEntity;

readonly class IndexByStatusLeadUseCase
{

    public function __construct(
        private LeadRepositoryInterface $repository,
        private StaffRepositoryInterface $staffRepository,
    )
    {
    }

    /**
     * @param int $staffId
     * @param string $status
     * @return LeadEntity[]
     */
    public function execute(int $staffId, string $status): array
    {
        $staff = $this->staffRepository->findById($staffId);

        /** @var StaffPosition $position */
        foreach ($staff->positions as $position) {

            if ($position->isAdmin() || $position->isSupervisor())
                return $this->repository->findByStatus($status);
        }
        return $this->repository->findByStatus($status, $staffId);
    }
}
