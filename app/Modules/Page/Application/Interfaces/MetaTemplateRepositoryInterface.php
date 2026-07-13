<?php

namespace App\Modules\Page\Application\Interfaces;

use App\Modules\Page\Domain\Entities\MetaTemplateEntity;

interface MetaTemplateRepositoryInterface
{
    /** @return MetaTemplateEntity[] */
    public function getAll(): array;

    public function getById(int $id): MetaTemplateEntity;

    public function getByClass(string $class): ?MetaTemplateEntity;

    public function getByEntity(string $entity): ?MetaTemplateEntity;

    public function existsByClass(string $class, ?int $excludeId = null): bool;

    public function existsByEntity(string $entity, ?int $excludeId = null): bool;

    public function save(MetaTemplateEntity $metaTemplate): MetaTemplateEntity;

    public function delete(int $id): void;
}
