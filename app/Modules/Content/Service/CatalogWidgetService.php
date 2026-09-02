<?php
declare(strict_types=1);

namespace App\Modules\Content\Service;

use App\Modules\Content\Entity\Widgets\BannerWidget;
use App\Modules\Content\Entity\Widgets\BannerWidgetItem;
use App\Modules\Content\Entity\Widgets\CatalogWidget;
use App\Modules\Content\Entity\Widgets\CatalogWidgetItem;
use App\Modules\Content\Entity\Widgets\WidgetItem;
use DB;
use Illuminate\Http\Request;

class CatalogWidgetService extends WidgetService
{

    public function create(Request $request): CatalogWidget
    {
        return CatalogWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
    }

    public function setWidget(CatalogWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);
    }

    public function addItem(int $widgetId, Request $request): void
    {
        $item = CatalogWidgetItem::register(
            $widgetId,
            $request->integer('modelId'),
            $request->string('modelType')->value());
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->save();

    }
    public function delItem(CatalogWidgetItem|WidgetItem $item): void
    {
        parent::delItem($item);
    }

    public function setItem(CatalogWidgetItem $item, Request $request): void
    {

        $item->model_id = $request->string('modelId')->trim()->value();
        $item->model_type = $request->string('modelType')->trim()->value();
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->save();
    }

}
