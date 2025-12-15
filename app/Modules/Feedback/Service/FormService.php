<?php

namespace App\Modules\Feedback\Service;

use App\Modules\Feedback\Entity\FormBack;
use App\Modules\Page\Entity\Widgets\FormWidget;
use App\Modules\User\Entity\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormService
{

    public function createForm(FormWidget $widget, Request $request): void
    {

        try {


            $form = FormBack::register($widget->id, $request->string('url')->trim()->value());
            $form->data_form = $request->all();
            $form->save();
            $form->refresh();
            //TODO Сделать через event, как Order

            $form->createLead($form->data()); //Создаем Лид
            if ($request->has('email')) {
                //TODO Влозможно перенести в сервис Lead
                $email = $request->string('email')->trim()->value();
                $user = User::where('email', $email)->first();
                if (!is_null($user)) {
                    $form->lead->user_id = $user->id;
                    $form->lead->save();
                }
            }
            if ($request->has('name')) {
                $form->lead->name = $request->string('name')->trim()->value();
                $form->lead->save();
            }

        } catch (\Throwable $e) {
            Log::info('FormService ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }


      //  event(new FormBackHasCreated($form));

    }
}
