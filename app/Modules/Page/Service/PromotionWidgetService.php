<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\PromotionWidget;
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
