<?php
declare(strict_types=1);

namespace App\Modules\Content\Repository;

use App\Modules\Catalog\Infrastructure\Models\Group;
use App\Modules\Content\Entity\Widgets\ProductWidget;
use App\Modules\Content\Entity\Widgets\ProductWidgetItem;
use Illuminate\Contracts\Support\Arrayable;

class ProductWidgetRepository
{

    public function getIndex(\Illuminate\Http\Request $request): Arrayable
    {
        return ProductWidget::with('modelable')
            ->get()
            ->map(fn(ProductWidget $widget) => $this->WidgetToArray($widget));
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
            'modelable_name' => $modelable?->name,
            'modelable_key' => $modelableKey !== false ? $modelableKey : null,
            //'modelable' => ProductGroupType::modelKey($widget->modelable_type),
            'model_type' => $widget->model_type,
            'modelable_id' => $widget->modelable_id,
        ]);
    }

}
