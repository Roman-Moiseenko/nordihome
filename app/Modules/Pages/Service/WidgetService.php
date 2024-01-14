<?php
declare(strict_types=1);

namespace App\Modules\Pages\Service;

use App\Modules\Pages\Entity\DataWidgetInterface;
use App\Modules\Pages\Entity\Widget;
use Illuminate\Http\Request;

class WidgetService
{

    public function create(Request $request)
    {
        $widget = Widget::register(
            $request['name'],
            $request['data_class'],
            (int)$request['data_id'],
            $request['template'],
            $request['params'] ?? [],
        );
        return $widget;
    }

    public function update(Request $request, Widget $widget)
    {
        $widget->name = $request['name'];
        $widget->data_class = $request['data_class'];
        $widget->data_id = $request['data_id'];
        $widget->template = $request['template'];
        $widget->params = $request['params'] ?? [];
        $widget->save();
        return $widget;
    }

    public function destroy(Widget $widget)
    {
        //TODO Если используется, удалить нельзя
        $widget->delete();
    }

    public function getIds(string $class): array
    {
        $result = [];
        /** @var DataWidgetInterface[] $items */
        $items = $class::orderBy('id')->get();
        foreach ($items as $item) {
            $result[$item->id] = $item->getDataWidget()->title;
        }
        return $result;
    }
}
