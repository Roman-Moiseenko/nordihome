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

    /** @return RoomEntity[] */
    public function getTree(): array;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;
}
