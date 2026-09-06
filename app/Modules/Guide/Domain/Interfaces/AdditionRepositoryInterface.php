<?php

namespace App\Modules\Guide\Domain\Interfaces;

use App\Modules\Guide\Domain\Entities\AdditionEntity;

interface AdditionRepositoryInterface
{
    public function getById(int $id): AdditionEntity;

    /**
     * @return AdditionEntity[]
     */
    public function getAll(): array;
    public function findBySlug(string $slug):? AdditionEntity;
    public function save(AdditionEntity $addition): AdditionEntity;

    public function remove(int $id): void;
    /**
     * @return AdditionEntity[]
     */
    public function getByType(int|string $type): array;
}
