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
                        <!-- Выбрать категорию -->
                        {{ \App\Forms\Input::create('surname', ['placeholder' => 'Фамилия', 'value' => $worker->fullname->surname ?? '', 'class' => 'mt-3'])->show() }}
                        {{ \App\Forms\Input::create('firstname', ['placeholder' => 'Имя', 'value' =>$worker->fullname->firstname ?? '', 'class' => 'mt-3'])->show() }}
                        {{ \App\Forms\Input::create('secondname', ['placeholder' => 'Отчество', 'value' => $worker->fullname->secondname ?? '', 'class' => 'mt-3'])->show() }}
                        {{ \App\Forms\Input::create('phone', ['placeholder' => 'Телефон', 'value' => $worker->phone ?? '', 'class' => 'mt-3'])->show() }}
                        {{ \App\Forms\Input::create('telegram_user_id', ['placeholder' => 'Телеграм ID', 'value' => $worker->telegram_user_id ?? '', 'class' => 'mt-3'])->show() }}
                        <x-base.form-label for="select-post" class="mt-3">Специализация</x-base.form-label>
                        <x-base.tom-select id="select-post" name="post" class="w-full" data-placeholder="Выберите специализацию">
                            <option value="0"></option>
                            @foreach($posts as $post => $name)
                                <option value="{{ $post }}"
                                    @if(!is_null($worker)){{ $worker->post == $post ? 'selected' : ''}}@endif>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>

                        <x-base.form-label for="select-storage" class="mt-3">Хранилище</x-base.form-label>
                        <x-base.tom-select id="select-storage" name="storage_id" class="w-full" data-placeholder="Выберите Хранилище">
                            <option value="0"></option>
                            @foreach($storages as $storage)
                                <option value="{{ $storage->id }}"
                                @if(!empty(old('storage_id')))
                                    {{ old('storage_id')  == $storage->id ? 'selected' : '' }}
                                    @else
                                        @if(!is_null($worker)){{ $worker->storage_id == $storage->id ? 'selected' : ''}}@endif
                                    @endif
                                >
                                    {{ $storage->name }}
                                </option>
                            @endforeach
                        </x-base.tom-select>
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
            <x-base.lucide icon="lightbulb" class="w-12 h-12 mr-2 text-warning/80 absolute top-0 right-0 mt-5 mr-3"/>
            <h2 class="text-lg font-medium">
                Информация
            </h2>
            <div class="mt-5 font-medium"></div>
            <div class="leading-relaxed mt-2 text-slate-600 dark:text-slate-500">
                <div>Поле <b>хранилище</b> привязывает работника к складу и ответственному по нему.</div>
                <div>Если для работника не имеет значение точка привязки, то Хранилище оставьте пустым</div>
            </div>
        </div>
    </div>
</div>


