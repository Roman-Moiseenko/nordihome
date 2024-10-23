<div class="company-detail">
    <x-infoBlock title="Налоговые сведения">
        <div class="mt-3">
            <div class="grid grid-cols-12 gap-x-6">
                <div class="col-span-12 lg:col-span-6">
                    {{ \App\Forms\Input::create('full_name', ['placeholder' => 'Полное название', 'class' => '',
'value' => !is_null($company) ? $company->full_name : ''])
                        ->label('Полное название')->show() }}
                    {{ \App\Forms\Input::create('short_name', ['placeholder' => 'Сокращенное название', 'class' => 'mt-3',
'value' => !is_null($company) ? $company->short_name : ''])
                        ->label('Сокращенное название')->show() }}
                </div>
                <div class="col-span-12 lg:col-span-6">
                    <div class="grid grid-cols-2 gap-x-6">
                        {{ \App\Forms\Input::create('inn', ['placeholder' => 'ИНН', 'class' => '','value' => !is_null($company) ? $company->inn : ''])
                           ->label('ИНН')->show() }}
                        {{ \App\Forms\Input::create('kpp', ['placeholder' => 'КПП', 'class' => '','value' => !is_null($company) ? $company->kpp : ''])
                            ->label('КПП')->show() }}
                    </div>
                    <div class="grid grid-cols-2 gap-x-6">
                        {{ \App\Forms\Input::create('ogrn', ['placeholder' => 'ОГРН', 'class' => 'mt-3','value' => !is_null($company) ? $company->ogrn : ''])
                            ->label('ОГРН')->show() }}

                        <div>
                            <x-base.form-label for="select-holding" class="mt-3">Холдинг</x-base.form-label>
                            <x-base.tom-select id="select-holding" name="holding_id" class="" data-placeholder="Создайте или выберите (один)" multiple>
                                <option value="0"></option>
                                @foreach($holdings as $holding)
                                    <option value="{{ $holding->id }}"
                                    @if(isset($company))
                                        {{ $holding->id == $company->holding_id ? 'selected' : ''}}
                                        @endif
                                    >{{ $holding->name }}</option>
                                @endforeach
                            </x-base.tom-select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-infoBlock>
</div>
