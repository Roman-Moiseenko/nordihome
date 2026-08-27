<?php

declare(strict_types=1);

namespace App\Modules\Discount\Application\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Контракт для работы со связью "акция — товары" (pivot-таблица promotions_products).
 */
interface PromotionProductRepositoryInterface
{
    /**
     * Получить ID товаров акции (с пагинацией).
     * Каждая строка содержит product_id и цену товара в акции (price).
     *
     * @param int $promotionId
     * @param int $perPage
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getProductIdsByPromotionId(int $promotionId, int $perPage = 15, int $page = 1): LengthAwarePaginator;

    /**
     * Привязать товары к акции (добавление к существующим).
     *
     * @param int $promotionId
     * @param array<int, float> $products [product_id => price]
     */
    public function attachProducts(int $promotionId, array $products): void;

    /**
     * Синхронизировать товары акции (заменить весь набор).
     *
     * @param int $promotionId
     * @param array<int, float> $products [product_id => price]
     */
    public function syncProducts(int $promotionId, array $products): void;

    /**
     * Отвязать товары от акции.
     *
     * @param int $promotionId
     * @param int[] $productIds
     */
    public function detachProducts(int $promotionId, array $productIds): void;

    /**
     * Установить цену товара в акции.
     *
     * @param int $promotionId
     * @param int $productId
     * @param float $price
     */
    public function setPrice(int $promotionId, int $productId, float $price): void;
}
