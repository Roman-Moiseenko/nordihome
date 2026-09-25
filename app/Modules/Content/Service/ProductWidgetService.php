<?php
declare(strict_types=1);

namespace App\Modules\Content\Service;

use App\Modules\Content\Domain\ValueObjects\ProductGroupType;
use App\Modules\Content\Entity\Widgets\ProductWidget;
use App\Modules\Content\Entity\Widgets\ProductWidgetItem;
use Illuminate\Http\Request;

class ProductWidgetService extends WidgetService
{

    public function create(Request $request): ProductWidget
    {
        $widget = ProductWidget::new(
            $request->string('name')->trim()->value(),
            $request->string('template')->trim()->value(),
        );

        $widget->modelable_id = $request->integer('modelable_id');
        $widget->model_type = $request->string('model_type')->trim()->value();
        $widget->modelable_type = ProductGroupType::modelClass($widget->model_type);
        $widget->caption = $request->string('caption')->trim()->value();
        $widget->description = $request->string('description')->trim()->value();
        $widget->button_name = $request->string('button_name')->trim()->value();
        $widget->url = $request->string('url')->trim()->value();
        $widget->save();

        return $widget;
    }

    public function setWidget(ProductWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);

        $widget->modelable_id = $request->integer('modelable_id');
        $widget->model_type = $request->string('model_type')->trim()->value();
        $widget->modelable_type = ProductGroupType::modelClass($widget->model_type);
        $widget->button_name = $request->string('button_name')->trim()->value();
        $widget->url = $request->string('url')->trim()->value();

        $widget->save();
    }

    public function destroy(ProductWidget $widget): void
    {
        if ($widget->isActive()) throw new \DomainException('Виджет активен, удалить нельзя');
        $widget->delete();
    }


}
