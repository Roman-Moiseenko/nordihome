<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterEntity;

interface AvitoParameterRepositoryInterface
{
    /** @return AvitoParameterEntity[] */
    public function getAll(): array;

    public function getById(int $id): AvitoParameterEntity;

    public function getBySlug(string $slug): ?AvitoParameterEntity;

    public function getByNameEn(string $nameEn): ?AvitoParameterEntity;

    public function save(AvitoParameterEntity $parameter): AvitoParameterEntity;
}
