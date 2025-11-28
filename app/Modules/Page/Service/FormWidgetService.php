<?php

namespace App\Modules\Page\Service;

use App\Modules\Page\Classes\ListForm;
use App\Modules\Page\Classes\ListItem;
use App\Modules\Page\Classes\ListsForm;
use App\Modules\Page\Entity\FormWidget;
use Illuminate\Http\Request;

class FormWidgetService extends WidgetService
{

    public function create(Request $request): FormWidget
    {
        $widget = FormWidget::register(
            $request->string('name')->trim()->value(),
            $request->string('template')->trim()->value(),
        );

        $widget->fields = [
            'name' => 'Имя',
            'email' => 'Email',
            'phone' => 'Телефон',
        ];
        $widget->save();
        return $widget;
    }


    public function destroy(FormWidget $widget): void
    {
        if ($widget->isActive()) throw new \DomainException('Виджет активен, удалить нельзя');
        $widget->delete();
    }

    public function setWidget(FormWidget $widget, Request $request): void
    {
        $this->setBase($widget, $request);

        $widget->save();
    }

    public function setFields(FormWidget $widget, Request $request)
    {
        $fields = [];
        $array = $request->input('fields');
        foreach ($array as $item) {
            $fields[$item['value']] = $item['label'];
        }
        $widget->fields = $fields;
        $widget->save();
    }

    public function setLists(FormWidget $widget, Request $request): void
    {
        $array = $request->input('lists');
        $lists = new ListsForm();
        foreach ($array as $key => $items) {
            $list = new ListForm();
            $list->slug = $key;
            foreach ($items as $item) {
                $list->items[] = $item['label'];
            }
            $lists->list[] = $list;
        }

        $widget->lists = $lists;
        $widget->save();
        //dd($request->all());
    }
}
