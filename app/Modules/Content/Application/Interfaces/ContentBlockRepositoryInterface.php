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
     * @return ContentBlockEntity[]
     */
    public function getTreeByContainer(string $containerType, int $containerId): array;
}
