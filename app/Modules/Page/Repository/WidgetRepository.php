<?php
declare(strict_types=1);

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\Widget;
use App\Modules\Page\Entity\WidgetItem;
use App\Modules\Product\Entity\Group;
use Illuminate\Contracts\Support\Arrayable;

class WidgetRepository
{

    public function getIndex(\Illuminate\Http\Request $request): Arrayable
    {
        return Widget::get()->map(fn(Widget $widget) => $this->WidgetToArray($widget));
    }

    private function WidgetToArray(Widget $widget): array
    {
        return array_merge($widget->toArray(), [
            'items' => $widget->items,
            'banner' => $widget->banner()->first(),
            'count' => $widget->items()->count(),
        ]);
    }

    public function WidgetWithToArray(Widget $widget): array
    {
        return array_merge($this->WidgetToArray($widget), [
            'items' => $widget->items()->get()->map(fn(WidgetItem $item) => array_merge($item->toArray(), [
                'image_file' => $item->getImage(),
                'group' => $item->group,
            ]))
        ]);
    }

    public function getGroups(Widget $widget): array
    {
        $ids = WidgetItem::where('widget_id', $widget->id)->pluck('group_id')->toArray();
        return Group::orderBy('name')->whereNotIn('id', $ids)->getModels();
    }
}
