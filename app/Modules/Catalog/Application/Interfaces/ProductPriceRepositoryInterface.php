<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Interfaces;

use App\Modules\Catalog\Domain\Entities\ProductPriceEntity;

interface ProductPriceRepositoryInterface
{
    /**
     * Найти цену по ID
     */
    public function getById(int $id): ProductPriceEntity;

    /**
     * Получить последнюю цену товара указанного типа
     */
    public function getLastByProductAndType(int $productId, string $type): ?ProductPriceEntity;

    /**
     * Получить все цены товара (по всем типам, сортировка по set_at DESC)
     *
     * @return ProductPriceEntity[]
     */
    public function getByProductId(int $productId): array;

    /**
     * Получить последнюю цену каждого типа для товара.
     * Возвращает ассоциативный массив [type => amount, ...]
     *
     * @return array<string, float>
     */
    public function findCurrentPrices(int $productId): array;

    /**
     * Сохранить (создать или обновить) цену
     */
    public function save(ProductPriceEntity $price): ProductPriceEntity;

    /**
     * Удалить цену по ID
     */
    public function delete(int $id): void;
}
