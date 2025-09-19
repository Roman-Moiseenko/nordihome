<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Widget;
use Illuminate\Http\Request;

abstract class WidgetService
{

    protected function setBase(Widget $widget, Request $request): void
    {
        $widget->name = $request->string('name')->trim()->value();
        $widget->template = $request->string('template')->trim()->value();
        $widget->caption = $request->string('caption')->trim()->value();
        $widget->description = $request->string('description')->trim()->value();
        $widget->name = $request->string('name')->trim()->value();

        $widget->saveImage($request->file('image'), $request->boolean('clear_image'));
        $widget->saveIcon($request->file('icon'), $request->boolean('clear_icon'));

        $widget->save();
    }


    public function delWidget(Widget $widget): void
    {
        if ($widget->active) throw new \DomainException('Нельзя удалить активный виджет');
        $widget->delete();
    }

    public function toggle(Widget $widget): void
    {
        $widget->active = !$widget->active;
        $widget->save();
    }

}
