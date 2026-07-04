<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Interfaces;

use App\Modules\Parser\Domain\Entities\ParserCategoryEntity;

interface ParserCategoryRepositoryInterface
{
    /** @return ParserCategoryEntity[] */
    public function getAll(): array;

    public function getById(int $id): ParserCategoryEntity;
    public function getByIkeaId(string $ikeaId):? ParserCategoryEntity;
    public function save(ParserCategoryEntity $category): ParserCategoryEntity;

    public function delete(int $id): void;

    /** @return ParserCategoryEntity[] */
    public function getTree(): array;

    public function existsByIkeaId(string $ikeaId): bool;

    public function findByIkeaId(string $ikeaId): ?ParserCategoryEntity;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;

    /** @return ParserCategoryEntity[] */
    public function getActiveRoots(): array;

    /** @return ParserCategoryEntity[] */
    public function getActiveLeaves(): array;

    public function hasChildren(int $id): bool;

    /** @return int[] */
    public function getDescendantIds(int $id): array;

    public function toggleActive(int $id): void;

    public function bulkToggleActive(array $ids, bool $active): void;
}
