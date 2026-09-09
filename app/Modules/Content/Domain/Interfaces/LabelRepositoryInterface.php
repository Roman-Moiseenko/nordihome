<?php

namespace App\Modules\Content\Domain\Interfaces;

use App\Modules\Content\Domain\Entities\LabelEntity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LabelRepositoryInterface
{
    /** @return LabelEntity[] */
    public function getAll(): array;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function save(LabelEntity $label): LabelEntity;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;

    public function findByName(string $name): ?LabelEntity;

    public function delete(int $labelId): void;

    public function getById(int $labelId): LabelEntity;

    /** @return LabelEntity[] */
    public function findByIds(array $labelIds): array;
}
