<?php
declare(strict_types=1);

namespace App\Modules\Page\Repository;

use App\Modules\Page\Entity\Widgets\ProductWidget;
use App\Modules\Page\Entity\Widgets\ProductWidgetItem;
use App\Modules\Product\Entity\Group;
use Illuminate\Contracts\Support\Arrayable;

class ProductWidgetRepository
{

    public function getIndex(\Illuminate\Http\Request $request): Arrayable
    {
        return ProductWidget::get()->map(fn(ProductWidget $widget) => $this->WidgetToArray($widget));
    }

    private function WidgetToArray(ProductWidget $widget): array
    {
        return array_merge($widget->toArray(), [
            'items' => $widget->items,
            'banner' => $widget->banner()->first(),
            'count' => $widget->items()->count(),
            'image' => $widget->getImage(),
            'icon' => $widget->getIcon(),
        ]);
    }

    public function WidgetWithToArray(ProductWidget $widget): array
    {
        return array_merge($this->WidgetToArray($widget), [
            'items' => $widget->items()->get()->map(fn(ProductWidgetItem $item) => array_merge($item->toArray(), [
                'image_file' => $item->getImage(),
                'group' => $item->group,
            ]))
        ]);
    }

    public function getGroups(ProductWidget $widget): array
    {
        $ids = ProductWidgetItem::where('widget_id', $widget->id)->pluck('group_id')->toArray();
        return Group::orderBy('name')->whereNotIn('id', $ids)->getModels();
    }
}
