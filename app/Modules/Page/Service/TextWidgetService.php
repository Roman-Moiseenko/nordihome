<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Entity\TextWidget;
use App\Modules\Page\Entity\TextWidgetItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TextWidgetService extends WidgetService
{

    public function create(Request $request): TextWidget
    {
        return TextWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->value()
        );
    }

    public function setWidget(TextWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);
    }

    public function delText(TextWidget $widget): void
    {
        if ($widget->active) throw new \DomainException('Нельзя удалить активный виджет');
        foreach ($widget->items as $item) {
            $this->delItem($item);
        }
        $widget->delete();
    }


    public function addItem(TextWidget $widget, Request $request): void
    {
        $caption = $request->string('caption')->trim()->value();
        $description = $request->string('caption')->trim()->value();

        TextWidgetItem::register($widget->id, $caption, $description);
    }

    public function setItem(TextWidgetItem $item, Request $request): void
    {
        $item->slug = $request->string('slug')->trim()->value();
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->text = $request->string('text')->trim()->value();
        $item->save();
    }

}
