<?php

namespace App\Modules\Catalog\Domain\Interfaces;

interface AttributeProductRepositoryInterface
{
    /**
     * Возвращает значение атрибута у товара.
     * Для variant — массив id, для скаляров — само значение.
     */
    public function valueOf(int $productId, int $attributeId): mixed;
}
