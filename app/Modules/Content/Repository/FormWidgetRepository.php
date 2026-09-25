<?php

namespace App\Modules\Content\Repository;

use App\Modules\Content\Entity\Widgets\FormWidget;
use Illuminate\Http\Request;

class FormWidgetRepository
{

    public function getIndex(Request $request)
    {
        return FormWidget::orderBy('name')->get()
            ->map(fn(FormWidget $widget) => $widget->toArray());
    }
}
