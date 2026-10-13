<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Product\EditPage;

/**
 * Данные панели «Размерный ряд» редактирования товара.
 */
class ViewProductSizesData
{
    /**
     * @param array<int, array{id: int, name: string}> $sizeGrids Список доступных размерных сеток
     * @param array<string, mixed>|null $sizeGrid Выбранная сетка (dimensions + активные values)
     * @param array<int, array{id: int, values: array<string, string>, isActive: bool}> $productSizes
     */
    public function __construct(
        public int $id,
        public ?int $sizeGridId,
        public array $sizeGrids,
        public ?array $sizeGrid,
        public array $productSizes,
    )
    {
    }
}
