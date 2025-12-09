<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\Widgets\ProductWidget;
use App\Modules\Page\Entity\Widgets\ProductWidgetItem;
use Illuminate\Http\Request;

class ProductWidgetService extends WidgetService
{

    public function create(Request $request): ProductWidget
    {
        return ProductWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->trim()->value(),
        );
    }

    public function setWidget(ProductWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);

        $widget->banner_id = $request->input('banner_id');
        $widget->params = $request['params'] ?? [];

        $widget->save();
    }

    public function destroy(ProductWidget $widget): void
    {
        if ($widget->isActive()) throw new \DomainException('Виджет активен, удалить нельзя');
        $widget->delete();
    }

    public function addItem(ProductWidget $widget, Request $request): void
    {
        $group_id = $request->integer('group_id');
        $item = ProductWidgetItem::register($widget->id, $group_id);
        $item->group->published = true;
        $item->group->save();
    }


    public function setItem(ProductWidgetItem $item, Request $request): void
    {
        $item->saveImage($request->file('file'), $request->boolean('clear_file'));

        $item->slug = $request->string('slug')->trim()->value();
        $item->url = $request->string('url')->trim()->value();
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->save();
    }

}
