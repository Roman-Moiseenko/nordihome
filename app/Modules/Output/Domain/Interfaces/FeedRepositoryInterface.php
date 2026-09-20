<?php

declare(strict_types=1);

namespace App\Modules\Output\Domain\Interfaces;

use App\Modules\Output\Domain\Entities\FeedEntity;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FeedRepositoryInterface
{
    public function save(FeedEntity $feed): FeedEntity;

    /**
     * Список фидов с пагинацией.
     *
     * @return LengthAwarePaginator<int, FeedEntity>
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    public function getById(int $id): FeedEntity;

    public function delete(int $id): void;
}
