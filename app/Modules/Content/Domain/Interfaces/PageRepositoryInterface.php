<?php

declare(strict_types=1);

namespace App\Modules\Content\Domain\Interfaces;

use App\Modules\Content\Domain\Entities\PageEntity;

interface PageRepositoryInterface
{
    /** @return PageEntity[] */
    public function getAll(): array;
    /** @return PageEntity[] */

    public function getRootAll(): array;

    public function getById(int $id): PageEntity;

    public function save(PageEntity $page): PageEntity;

    public function delete(int $id): void;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;

    public function move(int $id, int $position): void;
}
