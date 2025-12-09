<?php

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\Widgets\FormWidget;

class FormWidgetRepository
{

    public function getIndex(\Illuminate\Http\Request $request)
    {
        return FormWidget::orderBy('name')->get()
            ->map(fn(FormWidget $widget) => array_merge($widget->toArray(), []));
    }

    public function WidgetWithToArray(FormWidget $widget): array
    {
        return array_merge($widget->toArray(), [
            'image' => $widget->getImage(),
            'icon' => $widget->getIcon(),
        ]);
    }
}
