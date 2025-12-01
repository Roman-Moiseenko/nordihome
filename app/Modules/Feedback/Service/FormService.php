<?php

namespace App\Modules\Feedback\Service;

use App\Modules\Feedback\Entity\FormBack;
use App\Modules\Feedback\Events\FormBackHasCreated;
use App\Modules\Page\Entity\FormWidget;
use Illuminate\Http\Request;

class FormService
{

    public function createForm(FormWidget $widget, Request $request): void
    {

        $form = FormBack::register($widget->id, $request->string('url')->trim()->value());
        $form->data_form = $request->all();
        $form->save();
        $form->refresh();
        $form->createLead(); //Создаем Лид

      //  event(new FormBackHasCreated($form));

    }
}
