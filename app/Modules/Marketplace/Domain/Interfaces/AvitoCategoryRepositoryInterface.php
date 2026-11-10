<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

use App\Modules\Marketplace\Domain\Entities\AvitoCategoryEntity;

interface AvitoCategoryRepositoryInterface
{
    /** @return AvitoCategoryEntity[] */
    public function getTree(): array;

    public function getById(int $id): AvitoCategoryEntity;

    public function getBySlug(string $slug): ?AvitoCategoryEntity;

    public function save(AvitoCategoryEntity $category): AvitoCategoryEntity;

    public function delete(int $id): void;

    /**
     * Синхронизация дерева категорий с данными Авито.
     * Не очищает таблицу целиком: удаляет отсутствующие, upsert остальных по slug.
     *
     * @param array<int, array{slug: string, name: string, parent_slug: ?string}> $nodes
     */
    public function sync(array $nodes): void;

    /**
     * Листовые категории (без дочерних), необходимые для синхронизации параметров.
     *
     * @return AvitoCategoryEntity[]
     */
    public function getLeaves(): array;

    /**
     * Полный путь категорий: имена предков + сама категория, разделитель "\".
     *
     * @param int[] $ids
     * @return array<int, string> map id => полный путь
     */
    public function getPathNamesByIds(array $ids): array;
}
