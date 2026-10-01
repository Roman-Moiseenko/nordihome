<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Interfaces;

use App\Modules\Accounting\Domain\Entities\PriceOutboxEntity;

interface PriceOutboxRepositoryInterface
{
    /** @return PriceOutboxEntity[] */
    public function getAll(): array;

    public function getById(int $id): PriceOutboxEntity;

    public function save(PriceOutboxEntity $outbox): PriceOutboxEntity;

    public function delete(int $id): void;

    /** Установить progress = true для всех записей */
    public function markAllAsProgress(): void;

    /** Удалить все записи, у которых progress = true */
    public function deleteAllWithProgress(): void;

    /** Установить progress = false для записей, у которых progress = true */
    public function resetProgress(): void;
}
