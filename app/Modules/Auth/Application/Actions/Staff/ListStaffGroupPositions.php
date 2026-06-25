<?php

namespace App\Modules\Auth\Application\Actions\Staff;

use App\Modules\Auth\Application\DTOs\Staff\StaffGroupData;
use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

final readonly class ListStaffGroupPositions
{
    public function __construct(
        private StaffRepositoryInterface $staffRepository
    ) {}

    /**
     * @return array<string, StaffGroupData[]>
     */
    public function execute(): array
    {
        $allStaff = $this->staffRepository->findAll();

        $groups = [];
        foreach (StaffPosition::allowed() as $positionValue) {
            $groups[$positionValue] = [];
        }

        $managerValues = StaffPosition::managers();
        $workerValues = StaffPosition::workers();
        $managers = [];
        $workers = [];

        foreach ($allStaff as $staff) {
            $staffDto = StaffGroupData::fromEntity($staff);
            $staffPositionValues = $staff->positions->toArrayOfStrings();

            foreach ($staffPositionValues as $positionValue) {
                if (isset($groups[$positionValue])) {
                    $groups[$positionValue][] = $staffDto;
                }
            }

            // Если хотя бы одна из должностей сотрудника входит в managers
            if (!empty(array_intersect($staffPositionValues, $managerValues))) {
                $managers[] = $staffDto;
            }

            // Если хотя бы одна из должностей сотрудника входит в workers
            if (!empty(array_intersect($staffPositionValues, $workerValues))) {
                $workers[] = $staffDto;
            }
        }

        $groups['managers'] = $managers;
        $groups['workers'] = $workers;

        return $groups;
    }
}
