<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Widgets\PostWidget;

class PostWidgetService extends WidgetService
{

    public function create(\Illuminate\Http\Request $request): PostWidget
    {
        return PostWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
    }

    public function setWidget(PostWidget $widget, \Illuminate\Http\Request $request)
    {
        $this->setBase($widget, $request);
    }
}
