<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Domain\Entities\SizeGridEntity;

interface SizeGridRepositoryInterface
{
    /**
     * @return SizeGridEntity[]
     */
    public function getAll(): array;

    public function getById(int $id): SizeGridEntity;

    public function save(SizeGridEntity $sizeGrid): SizeGridEntity;

    public function delete(int $id): void;
}
