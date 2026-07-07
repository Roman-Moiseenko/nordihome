<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Interfaces;

use App\Modules\Parser\Domain\Entities\ParserLogEntity;
use App\Modules\Parser\Domain\Entities\ParserLogItemEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface ParserLogRepositoryInterface
{
    /**
     * Найти лог по дате (формат YYYY-MM-DD)
     */
    public function findByDate(string $date): ?ParserLogEntity;

    /**
     * Создать новую запись лога (date = today)
     */
    public function createLog(string $date): ParserLogEntity;

    /**
     * Сохранить/обновить лог (например, для прочтения)
     */
    public function saveLog(ParserLogEntity $log): ParserLogEntity;

    /**
     * Добавить запись в лог (ParserLogItem)
     */
    public function addLogItem(ParserLogItemEntity $item): ParserLogItemEntity;

    /**
     * Получить все логи с пагинацией, сортировка по date DESC
     *
     * @return LengthAwarePaginator<ParserLogEntity>
     */
    public function getLogsPaginated(int $perPage = 20): LengthAwarePaginator;

    /**
     * Получить все записи лога по log_id с пагинацией
     *
     * @return LengthAwarePaginator<ParserLogItemEntity>
     */
    public function getLogItemsPaginated(int $logId, int $perPage = 20): LengthAwarePaginator;

    /**
     * Получить лог по ID
     */
    public function getById(int $id): ParserLogEntity;

    /**
     * Получить записи лога по статусу с подгрузкой связанных данных
     *
     * @return ParserLogItemEntity[]
     */
    public function getLogItemsByStatus(int $logId, string $status): array;

    /**
     * Получить количество записей лога по статусам
     *
     * @return array{new: int, price_changed: int, deleted: int, error: int}
     */
    public function getLogItemsCountsByStatus(int $logId): array;
}
