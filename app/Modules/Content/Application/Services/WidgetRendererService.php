<?php

declare(strict_types=1);

namespace App\Modules\Content\Application\Services;

use App\Modules\Content\Application\Interfaces\WidgetInstanceRepositoryInterface;
use App\Modules\Content\Application\Interfaces\WidgetRepositoryInterface;
use Illuminate\Support\Facades\View;

class WidgetRendererService
{
    public function __construct(
        private WidgetInstanceRepositoryInterface $instanceRepository,
        private WidgetRepositoryInterface $widgetRepository,
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

        return view($viewName, [
            'params' => $instance->params,
            'widget' => $widget,
            'instance' => $instance,
        ])->render();
    }
}
