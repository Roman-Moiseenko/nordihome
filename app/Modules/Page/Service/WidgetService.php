<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\DataWidgetInterface;
use App\Modules\Page\Entity\Widget;
use App\Modules\Page\Entity\WidgetItem;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Service\GroupService;
use Illuminate\Http\Request;

class WidgetService
{

    public function create(Request $request): Widget
    {
        return Widget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->trim()->value(),
        );
    }

    public function setWidget(Request $request, Widget $widget): void
    {
        $widget->name = $request->string('name')->trim()->value();
        $widget->url = $request->string('url')->trim()->value();
        $widget->caption = $request->string('caption')->trim()->value();
        $widget->description = $request->string('description')->trim()->value();
        $widget->template = $request->string('template')->trim()->value();
        $widget->banner_id = $request->input('banner_id');
        $widget->params = $request['params'] ?? [];
        $widget->save();
    }

    public function destroy(Widget $widget): void
    {
        if ($widget->isActive()) throw new \DomainException('Виджет активен, удалить нельзя');
        $widget->delete();
    }

    public function toggle(Widget $widget): void
    {
        $widget->active = !$widget->active;
        $widget->save();
    }

    public function addItem(Widget $widget, Request $request): void
    {
        $group_id = $request->integer('group_id');
        $item = WidgetItem::register($widget->id, $group_id);
        $item->group->published = true;
        $item->group->save();
    }

    public function delItem(WidgetItem $item): void
    {
        $widget = $item->widget;
        $item->delete();
        foreach ($widget->items as $i => $_item) {
            $_item->sort = $i;
            $_item->save();
        }
    }

    public function setItem(WidgetItem $item, Request $request): void
    {
        $item->saveImage($request->file('file'), $request->boolean('clear_file'));

        $item->slug = $request->string('slug')->trim()->value();
        $item->url = $request->string('url')->trim()->value();
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->save();
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
