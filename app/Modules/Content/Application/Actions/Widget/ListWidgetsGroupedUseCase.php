<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Actions\Widget;

use App\Modules\Content\Application\DTOs\Widget\WidgetIndexData;
use App\Modules\Content\Application\DTOs\Widget\WidgetListByCategoryData;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Shared\Domain\Entities\UserPermission;

readonly class ListWidgetsGroupedUseCase
{
    public function __construct(
        private WidgetRepositoryInterface $widgetRepository,
    )
    {
    }

    /**
     * @return array<string, WidgetListByCategoryData>
     */
    public function execute(UserPermission $userPermission): array
    {
        if (!$userPermission->can('content.widget.view')) {
            throw new \DomainException('Доступ запрещён');
        }

        $widgets = $this->widgetRepository->getAll();

        $grouped = [];

        foreach (WidgetCategory::CATEGORIES as $key => $label) {
            $grouped[$key] = new WidgetListByCategoryData(
                key: $key,
                label: $label,
                widgets: [],
            );
        }

        foreach ($widgets as $widget) {
            $key = $widget->category->getValue();

            if (isset($grouped[$key])) {
                $grouped[$key]->widgets[] = WidgetIndexData::fromEntity($widget);
            }
        }

        return $grouped;
    }
}
