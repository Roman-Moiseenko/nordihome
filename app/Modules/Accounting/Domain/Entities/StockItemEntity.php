<?php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Entities;

/**
 * Строка отчёта по остаткам товаров.
 * Объединяет данные товара (артикул, главная категория) и остатков (кол-во, резерв).
 */
final class StockItemEntity
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $categoryName,
        public readonly int $quantity,
        public readonly int $reserve,
    ) {
    }
}
