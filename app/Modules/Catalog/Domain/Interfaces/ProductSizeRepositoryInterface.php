<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Interfaces;

use App\Modules\Catalog\Domain\Entities\ProductSizeEntity;
use App\Modules\Catalog\Domain\Entities\SizeGridEntity;

interface ProductSizeRepositoryInterface
{
    /**
     * @return ProductSizeEntity[]
     */
    public function getByProductId(int $productId): array;

    /**
     * ID товаров, у которых настроена указанная размерная сетка.
     *
     * @return int[]
     */
    public function getProductIdsBySizeGridId(int $sizeGridId): array;

    /**
     * Полностью перестраивает варианты размеров товара по размерной сетке.
     *
     * Новые варианты создаются активными. Для существующих вариантов сохраняется
     * их is_active (если не передан явно в $desiredActive). Варианты, исчезнувшие
     * из сетки, удаляются, а связанные записи stock обнуляются (products_sizes_id = null).
     *
     * @param array<string, bool> $desiredActive Карта "канонический ключ" => isActive
     */
    public function rebuildForProduct(int $productId, SizeGridEntity $grid, array $desiredActive = []): void;

    /**
     * Удаляет все варианты размеров товара (с обнулением связанного stock).
     */
    public function clearForProduct(int $productId): void;
}
