<?php

namespace App\Modules\Marketplace\Domain\Interfaces;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterRegistryEntity;

interface AvitoParameterRegistryRepositoryInterface
{
    /** @return AvitoParameterRegistryEntity[] */
    public function getAll(): array;

    public function getById(int $id): AvitoParameterRegistryEntity;

    public function getByNameEn(string $nameEn): ?AvitoParameterRegistryEntity;

    public function existsByNameEn(string $nameEn): bool;

    public function save(AvitoParameterRegistryEntity $entity): AvitoParameterRegistryEntity;
}
