<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Services;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\ValueObjects\ProductGroupType;
use App\Modules\Shop\Application\DTOs\Elements\WidgetPageData;
use App\Modules\Shop\Infrastructure\Persistence\Query\ProductIndexQueryRepository;

/**
 * Обогащает WidgetPageData:
 *  - поля с format: 'widget' — заменяет ID дочерних экземпляров виджетов на WidgetPageData;
 *  - поля с format: 'product_group' — загружает товары выбранной сущности из БД
 *    и кладёт массив карточек товаров в $params['products'].
 *
 * Максимальная вложенность = 1 (у дочерних виджетов нет своих детей).
 */
final readonly class WidgetDataEnricherService
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $instanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
        private ProductIndexQueryRepository $productIndexQueryRepository,
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
            $format = $prop['format'] ?? null;

            if ($format === 'widget') {
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

                continue;
            }

            if ($format === 'product_group') {
                $params['products'] = $this->loadGroupProducts($params[$propName] ?? null);
            }
        }

        return new WidgetPageData(
            id: $widget->id,
            category: $widget->category,
            slug: $widget->slug,
            params: $params,
        );
    }

    /**
     * Загружает товары сущности «Группа товаров» (category, room, group, promotion, series).
     *
     * @param array|null $group Параметры группы: entity_type, entity_id, title, limit
     * @return array<int, array<string, mixed>> Массив карточек товаров
     */
    private function loadGroupProducts(?array $group): array
    {
        if (!$group || empty($group['entity_type']) || empty($group['entity_id'])) {
            return [];
        }

        $modelClass = ProductGroupType::modelClass((string) $group['entity_type']);
        if ($modelClass === null) {
            return [];
        }

        $entity = $modelClass::find((int) $group['entity_id']);
        if ($entity === null) {
            return [];
        }

        $limit = max(0, (int) ($group['limit'] ?? 0));

        $products = $entity->products()
            ->where('products.published', true)
            ->when($limit > 0, fn ($query) => $query->limit($limit))
            ->get();

        $ids = $products->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (empty($ids)) {
            return [];
        }

        return $this->productIndexQueryRepository->loadSimpleProductCards($ids);
    }
}
