<?php
declare(strict_types=1);

namespace App\Modules\Content\Repository;

use App\Modules\Content\Domain\ValueObjects\ProductGroupType;
use App\Modules\Content\Entity\Widgets\ProductWidget;
use App\Modules\Content\Entity\Widgets\ProductWidgetItem;
use App\Modules\Catalog\Entity\Group;
use Illuminate\Contracts\Support\Arrayable;

class ProductWidgetRepository
{

    public function getIndex(\Illuminate\Http\Request $request): Arrayable
    {
        return ProductWidget::with('modelable')->get()->map(fn(ProductWidget $widget) => $this->WidgetToArray($widget));
    }

    private function WidgetToArray(ProductWidget $widget): array
    {
        $modelable = $widget->modelable;
        $modelableKey = array_search($widget->modelable_type, ProductWidget::MODELS, true);

        return array_merge([
            'id' => $widget->id,
            'name' => $widget->name,
            'template' => $widget->template,
            'caption' => $widget->caption,
            'description' => $widget->description,
            'button_name' => $widget->button_name,
            'url' => $widget->url,
            'active' => $widget->active,

            'image' => $widget->getImage(),
            'icon' => $widget->getIcon(),
            'modelable_name' => $modelable?->name,
            'modelable_key' => $modelableKey !== false ? $modelableKey : null,
            'modelable' => ProductGroupType::modelKey($widget->modelable_type),
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
