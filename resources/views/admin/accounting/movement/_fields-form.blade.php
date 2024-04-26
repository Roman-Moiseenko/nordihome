<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Основные данные -->
    <div class="intro-y col-span-12 lg:col-span-8">
        <div class="intro-y box">
            <div class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-6">
                        <x-base.form-label for="select-storage-out" class="mt-3">Хранилище Убытие</x-base.form-label>
                        <x-base.tom-select id="select-storage-out" name="storage_out" class="w-full" data-placeholder="Выберите хранилище">
                            <option value="0"></option>
                            @foreach($storages as $storage)
                                <option value="{{ $storage->id }}"
                                @if($movement)
                                    {{ $movement->storage_out == $storage->id ? 'selected' : ''}}
                                    @endif
                                >
                                    {{ $storage->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>

                        <x-base.form-label for="select-storage-in" class="mt-3">Хранилище Прибытие</x-base.form-label>
                        <x-base.tom-select id="select-storage-in" name="storage_in" class="w-full" data-placeholder="Выберите хранилище">
                            <option value="0"></option>
                            @foreach($storages as $storage)
                                <option value="{{ $storage->id }}"
                                @if($movement)
                                    {{ $movement->storage_in == $storage->id ? 'selected' : ''}}
                                    @endif
                                >
                                    {{ $storage->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
                        {{ \App\Forms\Input::create('comment', ['placeholder' => 'Комментарий', 'value' => $movement->comment ?? '', 'class' => 'mt-3'])->label('Комментарий')->show() }}

                    </div>
                    <div class="col-span-12 lg:col-span-6">

                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
    <div class="intro-y col-span-4 hidden 2xl:block">
        <div class="mt-10 bg-warning/20 dark:bg-darkmode-600 border border-warning dark:border-0 rounded-md relative p-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="lightbulb" data-lucide="lightbulb" class="lucide lucide-lightbulb w-12 h-12 text-warning/80 absolute top-0 right-0 mt-5 mr-3"><line x1="9" y1="18" x2="15" y2="18"></line><line x1="10" y1="22" x2="14" y2="22"></line><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0018 8 6 6 0 006 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 018.91 14"></path></svg>
            <h2 class="text-lg font-medium">
                Информация
            </h2>
            <div class="mt-5 font-medium"></div>
            <div class="leading-relaxed mt-2 text-slate-600 dark:text-slate-500">
                <div>Все поля являются обязательными.</div>
                <div></div>
            </div>
        </div>
    </div>
</div>

