<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Interfaces;

use App\Modules\Content\Domain\Entities\PostEntity;

interface PostRepositoryInterface
{
    /** @return PostEntity[] */
    public function getAll(): array;

    public function getById(int $id): PostEntity;

    public function save(PostEntity $post): PostEntity;

    public function delete(int $id): void;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;
}
