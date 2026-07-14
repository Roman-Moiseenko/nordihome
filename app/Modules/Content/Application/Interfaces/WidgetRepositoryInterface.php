<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Interfaces;

use App\Modules\Content\Domain\Entities\WidgetEntity;

interface WidgetRepositoryInterface
{
    /** @return WidgetEntity[] */
    public function getAll(): array;

    public function getById(int $id): WidgetEntity;

    public function save(WidgetEntity $widget): WidgetEntity;

    public function delete(int $id): void;

    public function existsSlug(string $slug, ?int $excludeId = null): bool;

    public function existsByCategoryAndSlug(string $category, string $slug, ?int $excludeId = null): bool;
}
