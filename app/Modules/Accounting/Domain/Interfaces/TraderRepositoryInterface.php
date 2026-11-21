<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Interfaces;

use App\Modules\Accounting\Domain\Entities\TraderEntity;

interface TraderRepositoryInterface
{
    public function getById(int $id): TraderEntity;

    public function save(TraderEntity $trader): TraderEntity;

    public function delete(int $id): void;

    /**
     * @return TraderEntity[]
     */
    public function getAll(): array;

    public function attachOrganization(int $traderId, int $organizationId): void;

    public function detachOrganization(int $traderId, int $organizationId): void;

    public function setDefaultOrganization(int $traderId, int $organizationId): void;

    public function getDefaultTraderId(): ?int;

    public function getDefault(): ?TraderEntity;
}
