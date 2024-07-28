<?php
declare(strict_types=1);

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\DataWidgetInterface;
use App\Modules\Page\Entity\Widget;
use Illuminate\Http\Request;

class WidgetService
{

    public function create(Request $request)
    {
        return Widget::register(
            $request->string('name')->trim()->value(),
            $request->string('data_class')->trim()->value(),
            $request->integer('data_id'),
            $request->string('template')->trim()->value(),
            $request['params'] ?? [],
        );
    }

    public function update(Request $request, Widget $widget)
    {
        $widget->name = $request->string('name')->trim()->value();
        $widget->data_class = $request->string('data_class')->trim()->value();
        $widget->data_id = $request->integer('data_id');
        $widget->template = $request->string('template')->trim()->value();
        $widget->params = $request['params'] ?? [];
        $widget->save();
        return $widget;
    }

    public function destroy(Widget $widget)
    {
        if ($widget->active == true) throw new \DomainException('Виджет активен, удалить нельзя');
        $widget->delete();
    }

    public function getIds(string $class): array
    {
        $result = [];
        /** @var DataWidgetInterface[] $items */
        /** @var Widget $class */
        $items = $class::orderBy('id')->get();
        foreach ($items as $item) {
            $result[$item->id] = $item->getDataWidget()->title;
        }
        return $result;
    }
}
