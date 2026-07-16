<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Interfaces;

use App\Modules\Content\Domain\Entities\ContentBlockEntity;

interface ContentBlockRepositoryInterface
{
    /** @return ContentBlockEntity[] */
    public function getAll(): array;

    public function getById(int $id): ContentBlockEntity;

    public function save(ContentBlockEntity $contentBlock): ContentBlockEntity;

    public function delete(int $id): void;

    /**
     * @return ContentBlockEntity[]
     */
    public function getByContainer(string $containerType, int $containerId): array;

    /**
     * Обновить порядок сортировки блока.
     * Пересчитывает sort_order для всех блоков в том же контейнере.
     */
    public function updateSortOrder(int $blockId, int $newSort): void;

}
