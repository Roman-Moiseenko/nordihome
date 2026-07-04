<?php

declare(strict_types=1);

namespace App\Modules\Shared\Application\Interfaces;

use App\Modules\Shared\Domain\Entities\PhotoEntity;
use App\Modules\Shared\Domain\ValueObjects\PhotoType;

interface PhotoRepositoryInterface
{
    public function getById(int $id): PhotoEntity;

    /**
     * Найти фото по id сущности и model_type + type
     */
    public function findByEntity(int $imageableId, string $modelType, PhotoType $type): ?PhotoEntity;

    /**
     * Найти фото для нескольких id сущностей.
     * Для каждого imageableId возвращается первое фото (по sort), если тип gallery.
     *
     * @param array<int> $imageableIds
     * @return array<int, string>  — массив пар imageableId => uploadUrl
     */
    public function findByEntities(array $imageableIds, string $modelType, PhotoType $type): array;

    public function save(PhotoEntity $photo): PhotoEntity;

    public function update(int $id, array $data): PhotoEntity;

    public function delete(int $id): void;
}
