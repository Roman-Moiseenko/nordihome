<?php

namespace App\Modules\Auth\Application\Actions\Staff;

use App\Modules\Auth\Application\DTOs\Staff\StaffCreateData;
use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\ValueObjects\FullName;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;


readonly class CreateStaffUseCase
{
    public function __construct(
        private StaffRepositoryInterface $staffRepository
    )
    {
    }

    /**
     * @throws \Throwable
     */
    public function execute(StaffCreateData $dto, ?UserPermission $permissions): StaffEntity
    {
        if (!is_null($permissions) && !$permissions->can('auth.employee.create'))
            throw new AccessDeniedException();

        $fullName = new FullName(implode(' ', array_filter([
            $dto->lastName,
            $dto->firstName,
            $dto->middleName,
        ])));


        $staff = new StaffEntity(
            $fullName,
            new StaffPositions($dto->positions),
        );

        return $this->staffRepository->save($staff);
    }

}
