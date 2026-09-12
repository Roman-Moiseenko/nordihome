<?php

declare(strict_types=1);

namespace App\Modules\Analytics\Domain\Interfaces;

use App\Modules\Analytics\Domain\Entities\ActionEntity;
use DateTimeImmutable;

interface ActionRepositoryInterface
{
    public function findById(int $id): ?ActionEntity;

    /** Создать действие. */
    public function create(ActionEntity $action): ActionEntity;

    /**
     * Все действия сессии (для отчёта «путь клиента»).
     *
     * @return ActionEntity[]
     */
    public function findBySession(int $sessionId): array;

    /**
     * Все действия посетителя (для истории).
     *
     * @return ActionEntity[]
     */
    public function findByVisitor(int $visitorId, int $limit = 100, int $offset = 0): array;

    /**
     * Действия по типу за период.
     *
     * @return ActionEntity[]
     */
    public function findByTypeInPeriod(string $actionType, DateTimeImmutable $from, DateTimeImmutable $to, int $limit = 5000): array;

    /** Количество действий конкретного типа для сущности за период. */
    public function countByEntity(string $actionType, string $entityType, int $entityId, DateTimeImmutable $from, DateTimeImmutable $to): int;

    /** Удалить старые записи. */
    public function deleteOlderThan(DateTimeImmutable $before): int;
}
