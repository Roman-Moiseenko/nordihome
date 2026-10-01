<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Domain\Entities\SeriesEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface SeriesRepositoryInterface
{
    public function getAll(int $perPage = 15, int $page = 1): LengthAwarePaginator;

    public function getById(int $id): SeriesEntity;

    public function save(SeriesEntity $series): SeriesEntity;

    public function delete(int $id): void;

    public function getByName(string $name): ?SeriesEntity;
}
