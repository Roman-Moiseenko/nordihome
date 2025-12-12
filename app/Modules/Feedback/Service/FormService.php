<?php

namespace App\Modules\Feedback\Service;

use App\Modules\Feedback\Entity\FormBack;
use App\Modules\Page\Entity\Widgets\FormWidget;
use App\Modules\User\Entity\User;
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
        if ($request->has('email')) {
            //TODO Влозможно перенести в сервис Lead
            $email = $request->string('email')->trim()->value();
            $user = User::where('email', $email)->first();
            $form->lead->user_id = $user->id;
            $form->lead->save();
        } else if ($request->has('name')) {
            $form->lead->name = $request->string('name')->trim()->value();
            $form->lead->save();
        }


      //  event(new FormBackHasCreated($form));

    }
}
