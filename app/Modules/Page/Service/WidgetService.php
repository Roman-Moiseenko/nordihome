<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Widget;
use App\Modules\Page\Entity\WidgetItem;
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
        if (isset($widget->items) && !is_null($widget->items)) {
            foreach ($widget->items as $item) {
                $this->delItem($item);
            }
        }
        $widget->delete();
    }

    public function delItem(WidgetItem $item)
    {
        $item->delete();
        foreach ($item->widget->items as $i => $_item) {
            $_item->sort = $i;
            $_item->save();
        }
    }

    public function toggle(Widget $widget): void
    {
        $widget->active = !$widget->active;
        $widget->save();
    }
    public function upItem(WidgetItem $item): void
    {

        $items = [];
        foreach ($item->widget->items as $_item) {
            $items[] = $_item;
        }
        for ($i = 1; $i < count($items); $i++) {
            if ($items[$i]->id == $item->id) {
                $prev = $items[$i - 1]->sort;
                $next = $items[$i]->sort;
                $items[$i]->update(['sort' => $prev]);
                $items[$i - 1]->update(['sort' => $next]);
            }
        }
    }

    public function downItem(WidgetItem $item): void
    {
        $items = [];
        foreach ($item->widget->items as $_item) {
            $items[] = $_item;
        }
        for ($i = 0; $i < count($items) - 1; $i++) {
            if ($items[$i]->id == $item->id) {
                $prev = $items[$i + 1]->sort;
                $next = $items[$i]->sort;
                $items[$i]->update(['sort' => $prev]);
                $items[$i + 1]->update(['sort' => $next]);
            }
        }
    }
}
