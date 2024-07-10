
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Основные данные -->
    <div class="col-span-12">
        <div class="box">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-4">

                        {{ \App\Forms\Input::create('name',
                        ['placeholder' => 'Подпись', 'value' => !is_null($contact) ? $contact->name : ''])
                        ->label('Имя')->show() }}

                        {{ \App\Forms\Input::create('icon',
                        ['placeholder' => 'fontawesome 6.0', 'class' => 'mt-3', 'value' => !is_null($contact) ? $contact->icon : ''])
                        ->label('Класс иконки')->show() }}

                        {{ \App\Forms\Input::create('color',
                        ['placeholder' => '#000000', 'class' => 'mt-3', 'value' => !is_null($contact) ? $contact->color : ''])
                        ->label('Цвет')->show() }}

                        {{ \App\Forms\Input::create('url',
                        ['placeholder' => 'https://t.me/....', 'class' => 'mt-3', 'value' => !is_null($contact) ? $contact->url : ''])
                        ->label('Ссылка')->show() }}

                        {{ \App\Forms\Input::create('type',
                        ['placeholder' => 'data-type', 'class' => 'mt-3', 'value' => !is_null($contact) ? $contact->type : ''])->type('number')
                        ->label('Тип')->show() }}

                    </div>
                    <div class="col-span-12 lg:col-span-4">

                    </div>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
</div>

