<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Domain\Entities\CategoryEntity;
interface CategoryRepositoryInterface
{
    /** @return CategoryEntity[] */
    public function getAll(): array;

    public function getById(int $id): CategoryEntity;

    public function save(CategoryEntity $category): CategoryEntity;

    public function delete(int $id): void;

    /** @return CategoryEntity[] */
    public function getTree(): array;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;

    public function existsByWpId(int $wpId): bool;

    public function moveUp(int $id): void;

    public function moveDown(int $id): void;

    /** @return int[] */
    public function getDescendantIds(int $id): array;

    public function hasChildren(int $id): bool;

    public function bulkTogglePublished(array $ids, bool $published): void;
}
