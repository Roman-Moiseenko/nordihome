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

    public function setText(TextWidget $widget, Request $request): void
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


    public function delItem(TextWidgetItem $item): void
    {
        $item->delete();
        //Пересортировка
        foreach ($item->textWidget->items as $i => $_item) {
            $_item->sort = $i;
            $_item->save();
        }
    }

    public function addItem(TextWidget $widget, Request $request): void
    {

        $caption = $request->string('caption')->trim()->value();
        $description = $request->string('caption')->trim()->value();

        TextWidgetItem::register($widget->id, $caption, $description);

    }

    public function setItem(TextWidgetItem $item, Request $request): void
    {
        $item->caption = $request->string('caption')->trim()->value();
        $item->description = $request->string('description')->trim()->value();
        $item->save();
    }

    public function upItem(TextWidgetItem $item): void
    {

        $items = [];
        foreach ($item->textWidget->items as $_item) {
            $items[] = $_item;
        }
        for ($i = 1; $i < count($items); $i++) {
            if ($items[$i]->id == $item->id) {
                $prev = $items[$i - 1]->sort;
                $next = $items[$i]->sort;
                $items[$i]->update(['sort' => $prev]);
                $items[$i - 1]->update(['sort' => $next]);
            }
        }
    }

    public function downItem(TextWidgetItem $item): void
    {
        $items = [];
        foreach ($item->textWidget->items as $_item) {
            $items[] = $_item;
        }
        for ($i = 0; $i < count($items) - 1; $i++) {
            if ($items[$i]->id == $item->id) {
                $prev = $items[$i + 1]->sort;
                $next = $items[$i]->sort;
                $items[$i]->update(['sort' => $prev]);
                $items[$i + 1]->update(['sort' => $next]);
            }
        }
    }
}
