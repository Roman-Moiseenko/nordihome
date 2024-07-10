<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Основные данные -->
    <div class="col-span-12 lg:col-span-8">
        <div class="box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-6">
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Название', 'value' => $distributor->name ?? '', 'class' => 'mt-5'])->label('Поставщик')->show() }}
                        <label for="select-currency" class="inline-block mb-2 mt-3">Валюта</label>
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
                    </div>
                    <div class="col-span-12 lg:col-span-6">

                    </div>
                </div>
            </div>
            <div class="col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
    <div class="col-span-4 hidden 2xl:block">
        <div class="mt-10 bg-warning/20 dark:bg-darkmode-600 border border-warning dark:border-0 rounded-md relative p-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="lightbulb" data-lucide="lightbulb" class="lucide lucide-lightbulb w-12 h-12 text-warning/80 absolute top-0 right-0 mt-5 mr-3"><line x1="9" y1="18" x2="15" y2="18"></line><line x1="10" y1="22" x2="14" y2="22"></line><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0018 8 6 6 0 006 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 018.91 14"></path></svg>
            <h2 class="text-lg font-medium">
                Информация
            </h2>
            <div class="mt-5 font-medium"></div>
            <div class="leading-relaxed mt-2 text-slate-600 dark:text-slate-500">
                <div><b>Название поставщика</b> является обязательным полем.</div>
            </div>
        </div>
    </div>
</div>
