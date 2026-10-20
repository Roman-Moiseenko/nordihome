<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\Services;

use App\Modules\Catalog\Domain\ValueObjects\AttributeName;

/**
 * Legacy-обёртка для совместимости.
 *
 * Работа с атрибутами товара перенесена на сущности в AttachVariantAttributeService.
 */
class AttachAttributeProductService
{
    public function __construct(
        private readonly AttachVariantAttributeService $attachVariantAttributeService,
    ) {
    }

    /**
     * Назначает товару атрибут "Цвет" с указанными вариантами.
     *
     * @param int   $productId ID товара
     * @param array $colors    Список необработанных названий цветов (например ["белый", "черный"])
     */
    public function SetColorAttribute(int $productId, array $colors): void
    {
        $this->attachVariantAttributeService->execute($productId, $colors, AttributeName::color());
    }

    /**
     * Назначает товару атрибут "Материал" с указанными вариантами.
     *
     * @param int   $productId ID товара
     * @param array $materials Список необработанных названий материалов (например ["дерево", "металл"])
     */
    public function SetMaterialAttribute(int $productId, array $materials): void
    {
        $this->attachVariantAttributeService->execute($productId, $materials, AttributeName::material());
    }
}
