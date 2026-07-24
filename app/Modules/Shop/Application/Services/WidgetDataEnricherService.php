<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Services;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Shop\Application\DTOs\Elements\WidgetPageData;

/**
 * Обогащает WidgetPageData: заменяет ID дочерних экземпляров виджетов
 * (поля с format: 'widget' в схеме) на полноценные WidgetPageData.
 *
 * Максимальная вложенность = 1 (у дочерних виджетов нет своих детей).
 */
final readonly class WidgetDataEnricherService
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $instanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
    ) {}

    /**
     * Обогатить массив ContentBlockPageData.
     *
     * @param WidgetPageData $widget
     * @return WidgetPageData
     */
    public function enrich(WidgetPageData $widget): WidgetPageData
    {
        // Загружаем тип виджета, чтобы получить схему
        $widgetEntity = $this->widgetRepository->getById($widget->id);
        $schema = $widgetEntity->schema->toArray();
        $properties = $schema['properties'] ?? [];

        $params = $widget->params;

        foreach ($properties as $propName => $prop) {
            if (($prop['format'] ?? null) !== 'widget') {
                continue;
            }

            $childId = $params[$propName] ?? null;
            if ($childId === null) {
                continue;
            }

            $childInstance = $this->instanceRepository->getById((int) $childId);
            if ($childInstance === null) {
                continue;
            }

            $childWidget = $this->widgetRepository->getById($childInstance->widgetId);

            $params[$propName] = new WidgetPageData(
                id: $childInstance->id,
                category: $childWidget->category->getValue(),
                slug: $childWidget->slug,
                params: $childInstance->params,
            );
        }

        return new WidgetPageData(
            id: $widget->id,
            category: $widget->category,
            slug: $widget->slug,
            params: $params,
        );
    }
}
