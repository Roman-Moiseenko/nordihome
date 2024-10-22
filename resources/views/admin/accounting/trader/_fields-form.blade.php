<div class="grid grid-cols-12 gap-4 mt-5">
    <!-- Основные данные -->
    <div class="col-span-12 lg:col-span-12">
        <x-infoBlock title="Основные данные">
            <div class="mt-3">
                <div class="grid grid-cols-3 gap-x-6">
                    <div class="">
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Название', 'value' => $trader->name ?? '', 'class' => ''])
                            ->label('Название в CRM')->show() }}
                    </div>

                    <div class="">
                        <label for="select-organization" class="inline-block mb-2">Организация</label>
                        <x-base.tom-select id="select-organization" name="organization_id" class="w-full" data-placeholder="Выберите Организацию">
                            <option value="0"></option>
                            @foreach($organizations as $organization)
                                <option value="{{ $organization->id }}"
                                @if($trader)
                                    {{ $trader->organization_id == $organization->id ? 'selected' : ''}}
                                    @endif
                                >
                                    {{ $organization->short_name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
                    </div>
                    <div class="">
                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-default" type="checkbox" name="default"
                                                      checked="{{ !is_null($trader) ? ($trader->default ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-default">Организация по умолчанию</x-base.form-switch.label>
                        </x-base.form-switch>

                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-active" type="checkbox" name="active"
                                                      checked="{{ !is_null($trader) ? ($trader->active ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-active">Активна</x-base.form-switch.label>
                        </x-base.form-switch>
                    </div>
                </div>
            </div>
        </x-infoBlock>
        <button type="submit" class="btn btn-primary shadow-md mt-5">Сохранить</button>
    </div>
</div>
