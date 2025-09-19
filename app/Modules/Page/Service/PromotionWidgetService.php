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

    public function setWidget(PromotionWidget $promotion, Request $request): void
    {
        $this->setBase($promotion, $request);

        $promotion->promotion_id = $request->integer('promotion_id');
        $promotion->banner_id = $request->input('banner_id');

        $promotion->save();
    }


}
