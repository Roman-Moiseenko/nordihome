<?php

namespace App\Modules\Content\Service;

use App\Modules\Content\Entity\Widgets\PromotionWidget;
use Illuminate\Http\Request;

class PromotionWidgetService extends WidgetService
{

    public function create(Request $request): PromotionWidget
    {
        return PromotionWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
    }

    public function setWidget(PromotionWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);

        $widget->promotion_id = $request->integer('promotion_id');
        $widget->banner_id = $request->input('banner_id');

        $widget->save();
    }


}
