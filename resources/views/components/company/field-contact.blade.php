<div>
    <x-infoBlock title="Контакты">
        <div class="mt-3">
            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('phone', ['placeholder' => 'Телефон', 'class' => '',
                        'value' => !is_null($company) ? $company->phone : ''])
                       ->label('Телефон')->show() }}
                    {{ \App\Forms\Input::create('email', ['placeholder' => 'Email', 'class' => 'mt-3',
                    'value' => !is_null($company) ? $company->email : ''])
                        ->label('Email')->show() }}
                </div>
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('post', ['placeholder' => 'Должность руководителя', 'class' => '',
                        'value' => !is_null($company) ? $company->post :''])
                        ->label('Должность руководителя')->show() }}
                    <div class="flex">
                        {{ \App\Forms\Input::create('chief[surname]', ['placeholder' => 'Фамилия', 'class' => 'mt-3',
                             'value' => !is_null($company) ? $company->chief->surname : ''])
                             ->label('Фамилия')->show() }}
                        {{ \App\Forms\Input::create('chief[firstname]', ['placeholder' => 'Имя', 'class' => 'mt-3 ml-2',
                            'value' => !is_null($company) ? $company->chief->firstname : ''])
                            ->label('Имя')->show() }}
                        {{ \App\Forms\Input::create('chief[secondname]', ['placeholder' => 'Отчество', 'class' => 'mt-3 ml-2',
                            'value' => !is_null($company) ? $company->chief->secondname : ''])
                            ->label('Отчество')->show() }}
                    </div>

                </div>
            </div>
        </div>
    </x-infoBlock>
</div>
