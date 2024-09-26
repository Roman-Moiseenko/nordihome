<div>
    <x-infoBlock :title="$title">
        <div class="mt-3">
            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-2">
                    {{ \App\Forms\Input::create($field . '[post]', ['placeholder' => 'Почтовый индекс', 'class' => 'mt-3', 'value' => $address->post])
                        ->label('Почтовый индекс')->show() }}
                </div>
                <div class="col-span-12 lg:col-span-4">
                    {{ \App\Forms\Input::create($field . '[region]', ['placeholder' => 'Регион', 'class' => 'mt-3', 'value' => $address->region])
                        ->label('Регион')->show() }}
                </div>
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create($field . '[address]', ['placeholder' => 'Адрес', 'class' => 'mt-3', 'value' => $address->address])
                        ->label('Адрес')->show() }}
                </div>
                @if($map)
                    <div class="col-span-12 lg:col-span-4">
                        Координаты для отображения на карте
                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        {{ \App\Forms\Input::create($field . '[latitude]', ['placeholder' => 'Широта', 'class' => 'mt-3', 'value' => $address->latitude])
                        ->label('Широта')->show() }}
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        {{ \App\Forms\Input::create($field . '[longitude]', ['placeholder' => 'Долгота', 'class' => 'mt-3', 'value' => $address->longitude])
                        ->label('Долгота')->show() }}
                    </div>
                @endif
            </div>
        </div>
    </x-infoBlock>
</div>
