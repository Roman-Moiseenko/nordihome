<?php

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Domain\Entities\TagEntity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TagRepositoryInterface
{

    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function save(TagEntity $tag): TagEntity;

    public function existsSlug(string $slug, int $tagId): bool;

    public function findByName(string $name);

    public function delete(int $tagId);

    public function getById(int $tagId): TagEntity;

    public function findByIds(array $tagIds): array;
}
