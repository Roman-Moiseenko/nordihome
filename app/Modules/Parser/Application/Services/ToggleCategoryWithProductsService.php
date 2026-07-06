<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\Services;

use App\Modules\Parser\Application\Actions\Category\ToggleCategoryActiveUseCase;
use App\Modules\Parser\Application\Actions\Category\ToggleProductsAvailabilityUseCase;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ToggleCategoryWithProductsService
{
    public function __construct(
        private ToggleCategoryActiveUseCase $toggleCategoryActiveUseCase,
        private ToggleProductsAvailabilityUseCase $toggleProductsAvailabilityUseCase,
    ) {}

    /**
     * Переключает категорию (и дочерние) и все товары из этих категорий.
     * Возвращает сообщение для flash-уведомления.
     */
    public function execute(int $categoryId, UserPermission $userPermission): string
    {
        // 1. Переключаем категорию и дочерние
        $newActive = $this->toggleCategoryActiveUseCase->execute($categoryId, $userPermission);

        // 2. Получаем ID дочерних категорий и все товары, переключаем availability
        $affectedProducts = $this->toggleProductsAvailabilityUseCase->execute(
            $categoryId,
            $newActive,
            $userPermission,
        );

        $message = $newActive
            ? "Категория(и) добавлена(ы) в парсинг"
            : "Категория(и) убрана(ы) из парсинга";

        if ($affectedProducts > 0) {
            $message .= ". Затронуто товаров: {$affectedProducts}";
        }

        return $message;
    }
}
