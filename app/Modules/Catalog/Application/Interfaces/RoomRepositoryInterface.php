<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Domain\Entities\RoomEntity;
interface RoomRepositoryInterface
{
    /** @return RoomEntity[] */
    public function getAll(): array;

    public function getById(int $id): RoomEntity;

    public function save(RoomEntity $room): RoomEntity;

    public function delete(int $id): void;

    /**
     * @param int[] $ids
     * @return RoomEntity[]
     */
    public function findByIds(array $ids): array;

    /** @return RoomEntity[] */
    public function getTree(): array;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;
    public function findByWpId(int $wpId): ?RoomEntity;
    public function existsByWpId(int $wpId): bool;

    public function moveUp(int $id): void;

    public function moveDown(int $id): void;

    /** @return int[] */
    public function getDescendantIds(int $id): array;

    public function bulkTogglePublished(array $ids, bool $published): void;

    public function hasChildren(int $id): bool;
}
