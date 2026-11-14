<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Services;

use App\Modules\Content\Domain\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;
use Illuminate\Support\Facades\View;

class WidgetRendererService
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $instanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
        private WidgetImageResolverService $imageResolver,
    ) {}

    /**
     * Рендерит экземпляр виджета по ID.
     *
     * @param int $instanceId
     * @return string HTML
     */
    public function renderInstance(int $instanceId): string
    {
        $instance = $this->instanceRepository->getById($instanceId);
        $widget = $this->widgetRepository->getById($instance->widgetId);

        $viewName = 'widgets.' . $widget->category->getValue() . '.' . $widget->slug;

        if (!View::exists($viewName)) {
            return "<!-- Widget [{$widget->name}]: шаблон {$viewName} не найден -->";
        }

        // Актуализируем src у изображений через текущий драйвер хранилища (local/s3),
        // чтобы смена хранилища или домена S3 не ломала уже сохранённые виджеты.
        $params = $this->imageResolver->resolve($instance->params, $widget->schema->toArray());

        return view($viewName, [
            'params' => $params,
            'widget' => $widget,
            'instance' => $instance,
        ])->render();
    }
}
