<div class="box p-5 mt-5 block-menus-product">
    <div class="rounded-md border border-slate-200/60 p-5 dark:border-darkmode-400">
        <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium dark:border-darkmode-400">
            <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/> Налоговые сведения
        </div>
        <div class="mt-5">

        <div class="grid grid-cols-12 gap-x-6">
            <div class="col-span-12 lg:col-span-6">
                {{ \App\Forms\Input::create('name', ['placeholder' => 'Полное название', 'class' => 'mt-6', 'value' => (isset($organization) ? $organization->name : '')])
                    ->label('Полное название')->show() }}
                {{ \App\Forms\Input::create('short_name', ['placeholder' => 'Сокращенное название', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->short_name : '')])
                    ->label('Сокращенное название')->show() }}
                {{ \App\Forms\Input::create('INN', ['placeholder' => 'ИНН', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->INN : '')])
                    ->label('ИНН')->show() }}
                {{ \App\Forms\Input::create('KPP', ['placeholder' => 'КПП', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->KPP : '')])
                    ->label('КПП')->show() }}
            </div>
            <div class="col-span-12 lg:col-span-6">
                {{ \App\Forms\Input::create('OGRN', ['placeholder' => 'ОГРН', 'class' => 'mt-6', 'value' => (isset($organization) ? $organization->OGRN : '')])
                    ->label('ОГРН')->show() }}
                {{ \App\Forms\Input::create('index', ['placeholder' => 'Почтовый индекс', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->address->post : '')])
                    ->label('Почтовый индекс')->show() }}
                {{ \App\Forms\Input::create('address', ['placeholder' => 'Юридический адрес', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->address->address : '')])
                    ->label('Юридический адрес')->help('Без почтового индекса')->show() }}
            </div>
        </div>

        </div>
    </div>
</div>


<div class="box p-5 mt-5 block-menus-product">
    <div class="rounded-md border border-slate-200/60 p-5 dark:border-darkmode-400">
        <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium dark:border-darkmode-400">
            <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/> Банковские сведения
        </div>
        <div class="mt-5">

            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('BIK', ['placeholder' => 'БИК', 'class' => 'mt-6', 'value' => (isset($organization) ? $organization->BIK : '')])
                        ->label('БИК')->show() }}
                    {{ \App\Forms\Input::create('bank', ['placeholder' => 'Название банка', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->bank : '')])
                        ->label('Название банка')->show() }}
                </div>
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('corr_account', ['placeholder' => 'Корр/счет', 'class' => 'mt-6', 'value' => (isset($organization) ? $organization->corr_account : '')])
                        ->label('Корр/счет')->show() }}
                    {{ \App\Forms\Input::create('account', ['placeholder' => 'Расчетный счет', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->account : '')])
                        ->label('Расчетный счет')->show() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box p-5 mt-5 block-menus-product">
    <div class="rounded-md border border-slate-200/60 p-5 dark:border-darkmode-400">
        <div class="flex items-center border-b border-slate-200/60 pb-5 text-base font-medium dark:border-darkmode-400">
            <x-base.lucide class="mr-2 h-4 w-4" icon="ChevronDown"/> Контактные данные
        </div>
        <div class="mt-5">

            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('email', ['placeholder' => 'email', 'class' => 'mt-6', 'value' => (isset($organization) ? $organization->email : '')])
                        ->label('email')->show() }}
                    {{ \App\Forms\Input::create('phone', ['placeholder' => 'Телефон', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->phone : '')])
                        ->label('Телефон')->show() }}
                </div>
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('post_chief', ['placeholder' => 'Должность руководителя', 'class' => 'mt-6', 'value' => (isset($organization) ? $organization->post_chief : '')])
                        ->label('Должность руководителя')->show() }}
                    <div class="flex">
                        {{ \App\Forms\Input::create('surname', ['placeholder' => 'Фамилия', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->chief->surname : '')])
                            ->label('Фамилия')->show() }}
                        {{ \App\Forms\Input::create('firstname', ['placeholder' => 'Имя', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->chief->firstname : '')])
                            ->label('Имя')->show() }}
                        {{ \App\Forms\Input::create('secondname', ['placeholder' => 'Отчество', 'class' => 'mt-3', 'value' => (isset($organization) ? $organization->chief->secondname : '')])
                            ->label('Отчество')->show() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-base.form-switch class="mt-3">
    <x-base.form-switch.input id="checkbox-default" type="checkbox" name="default"
                              checked="{{ !is_null($organization) ? ($organization->default ? 'checked' : '') : '' }}"/>
    <x-base.form-switch.label for="checkbox-default">Организация по умолчанию</x-base.form-switch.label>
</x-base.form-switch>
