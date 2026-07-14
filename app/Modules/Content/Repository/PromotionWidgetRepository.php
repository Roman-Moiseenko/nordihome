<?php

namespace App\Modules\Content\Repository;

use App\Modules\Content\Entity\Widgets\PromotionWidget;
use Illuminate\Http\Request;

class PromotionWidgetRepository
{

    public function getIndex(Request $request)
    {
        return PromotionWidget::orderBy('name')->get()->map(fn(PromotionWidget $widget) => array_merge($widget->toArray(), [
            'promotion' => $widget->promotion_id == null ? null : $widget->promotion->name,

        ]));
    }

    public function PromotionWithToArray(PromotionWidget $widget): array
    {
        return array_merge($widget->toArray(), [
            'banner' => $widget->banner,
            'promotion' => $widget->promotion_id == null ? null : $widget->promotion,
            'image' => $widget->getImage(),
            'icon' => $widget->getIcon(),
        ]);
    }
}
