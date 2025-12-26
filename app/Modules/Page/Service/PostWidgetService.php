<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Widgets\PostWidget;
use Illuminate\Http\Request;

class PostWidgetService extends WidgetService
{

    public function create(Request $request): PostWidget
    {
        return PostWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
    }

    public function setWidget(PostWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);

        $widget->category_id = $request->input('category_id');
        $widget->save();
    }
}
