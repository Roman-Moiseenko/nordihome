<?php

namespace App\Modules\Feedback\Service;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Content\Entity\Widgets\FormWidget;
use App\Modules\Feedback\Infrastructure\Models\FormBack;
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
                $client = Client::where('email', $email)->first();
                if (!is_null($client)) {
                    $form->lead->client_id = $client->id;
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

    public function createFeedback(Request $request)
    {
        //MAINDO Создать Запись ответов без учета widget_id

    }
}
