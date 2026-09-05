<?php

declare(strict_types=1);

namespace App\Modules\Discount\Domain\Interfaces;

use App\Modules\Discount\Domain\Entities\PromotionEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface PromotionRepositoryInterface
{
    /**
     * Получить акцию по ID.
     *
     * @param int $id
     * @return PromotionEntity
     */
    public function getById(int $id): PromotionEntity;

    /**
     * Сохранить акцию (создать или обновить).
     *
     * @param PromotionEntity $promotion
     * @return PromotionEntity
     */
    public function save(PromotionEntity $promotion): PromotionEntity;

    /**
     * Получить все акции со статусом STARTED (запущенные).
     *
     * @return PromotionEntity[]
     */
    public function getAllStarted(): array;

    /**
     * Получить все акции.
     *
     * @return PromotionEntity[]
     */
    public function getAll(): array;

    /**
     * Получить все акции с пагинацией.
     *
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator<PromotionEntity>
     */
    public function getAllPaginated(int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Удалить акцию по ID.
     *
     * @param int $id
     */
    public function delete(int $id): void;
}
