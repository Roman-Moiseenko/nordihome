<?php

namespace App\Modules\Auth\Application\Interfaces;

use App\Modules\Auth\Domain\Entities\StaffEntity;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;
use App\Modules\Auth\Domain\ValueObjects\StaffPositions;
use Illuminate\Pagination\LengthAwarePaginator;

interface StaffRepositoryInterface
{
    public function save(StaffEntity $staff): StaffEntity;
    public function findById(int $id): ?StaffEntity;

    /**
     * @return StaffEntity[]
     */
    public function findAll(): array;

    /**
     * @param StaffPosition|StaffPositions $position
     * @return StaffEntity[]
     */
    public function findByPosition(StaffPosition|StaffPositions $position): array;
    public function findByUserId(int $userId): ?StaffEntity;
    public function delete(int $id): bool;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
