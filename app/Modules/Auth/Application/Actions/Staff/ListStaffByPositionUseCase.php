<?php

namespace App\Modules\Auth\Application\Actions\Staff;

use App\Modules\Auth\Application\Interfaces\StaffRepositoryInterface;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\Exceptions\AccessDeniedException;

final readonly class ListStaffByPositionUseCase
{
    public function __construct(private StaffRepositoryInterface $staffRepository) {}

    /**
     * @param StaffPosition|StaffPosition[] $position
     */
    public function execute(StaffPosition|array $position, ?UserPermission $permissions = null): array
    {
        if (!is_null($permissions) && !$permissions->can('auth.employee.view')) {
            throw new AccessDeniedException();
        }

        $positions = is_array($position)
            ? new StaffPositions(array_map(fn(StaffPosition $p) => $p->getValue(), $position))
            : $position;

        return $this->staffRepository->findByPosition($positions);
    }
}

