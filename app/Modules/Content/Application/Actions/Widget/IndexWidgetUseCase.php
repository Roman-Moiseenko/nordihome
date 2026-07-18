<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class IndexWidgetUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    )
    {
    }

    /**
     * @return WidgetEntity[]
     */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('content.widget.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        return $this->widgetRepository->getAll();
    }

    /**
     * Возвращает количество виджетов по каждой категории
     * @return array<string, int>
     */
    public function getCountByCategory(): array
    {
        $widgets = $this->widgetRepository->getAll();
        $counts = [];

        foreach (WidgetCategory::CATEGORIES as $key => $label) {
            $counts[$key] = 0;
        }

        foreach ($widgets as $widget) {
            $category = (string) $widget->category;
            if (array_key_exists($category, $counts)) {
                $counts[$category]++;
            }
        }

        return $counts;
    }


    /**
     * Возвращает общее количество виджетов
     */
    public function getTotalCount(): int
    {
        return count($this->widgetRepository->getAll());
    }
}
