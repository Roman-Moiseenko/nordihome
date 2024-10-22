<div class="grid grid-cols-12 gap-4 mt-5">
    <!-- Основные данные -->
    <div class="col-span-12 lg:col-span-12">
        <x-infoBlock title="Основные данные">
            <div class="mt-3">
                <div class="grid grid-cols-3 gap-x-6">
                    <div class="">
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Название', 'value' => $distributor->name ?? '', 'class' => ''])
                            ->label('Название в CRM')->show() }}
                    </div>
                    <div class="">
                        <label for="select-currency" class="inline-block mb-2">Валюта</label>
                        <x-base.tom-select id="select-currency" name="currency_id" class="w-full" data-placeholder="Выберите валюту документа">
                            <option value="0"></option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}"
                                @if($distributor)
                                    {{ $distributor->currency_id == $currency->id ? 'selected' : ''}}
                                    @endif
                                >
                                    {{ $currency->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
                        @error('currency_id')
                        <div class="pristine-error text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="">
                        <label for="select-organization" class="inline-block mb-2">Организация</label>
                        <x-base.tom-select id="select-organization" name="organization_id" class="w-full" data-placeholder="Выберите Организацию">
                            <option value="0"></option>
                            @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}"
                                @if($distributor)
                                    {{ $distributor->organization_id == $organization->id ? 'selected' : ''}}
                                    @endif
                                >
                                    {{ $organization->short_name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
                    </div>
                </div>
            </div>
        </x-infoBlock>
        <button type="submit" class="btn btn-primary shadow-md mt-5">Сохранить</button>
    </div>
</div>
