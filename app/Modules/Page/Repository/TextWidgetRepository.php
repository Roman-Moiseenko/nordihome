<?php

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\TextWidget;
use App\Modules\Page\Entity\TextWidgetItem;

class TextWidgetRepository
{

    public function getIndex(\Illuminate\Http\Request $request)
    {
        return TextWidget::orderBy('name')->get()->map(fn(TextWidget $widget) => array_merge($widget->toArray(), [
            'count' => $widget->items()->count(),
        ]));
    }

    public function TextWithToArray(TextWidget $widget): array
    {
        return array_merge($widget->toArray(), [
            'items' => $widget->items()->get()->map(fn(TextWidgetItem $item) => array_merge($item->toArray(), [
            ])),
            'image' => $widget->getImage(),
            'icon' => $widget->getIcon(),
        ]);
    }
}
