<div class="grid grid-cols-12 gap-4 mt-5">
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
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Название', 'value' => $storage->name ?? '', 'class' => 'mt-5'])->show() }}
                        {{ \App\Forms\Input::create('slug', ['placeholder' => 'Ссылка/slug', 'value' => $storage->slug ?? '', 'class' => 'mt-3'])->show() }}

                        {{ \App\Forms\Input::create('post', ['placeholder' => 'Почтовый индекс', 'value' => $storage->post ?? '', 'class' => 'mt-5'])->show() }}
                        {{ \App\Forms\Input::create('city', ['placeholder' => 'Город', 'value' => $storage->city ?? '', 'class' => 'mt-5'])->show() }}
                        {{ \App\Forms\Input::create('address', ['placeholder' => 'Адрес', 'value' => $storage->address ?? '', 'class' => 'mt-3'])->show() }}

                        {{ \App\Forms\Input::create('latitude', ['placeholder' => 'latitude', 'value' => $storage->latitude ?? '', 'class' => 'mt-5'])->show() }}
                        {{ \App\Forms\Input::create('longitude', ['placeholder' => 'longitude', 'value' => $storage->longitude ?? '', 'class' => 'mt-3'])->show() }}
                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-sale" type="checkbox" name="sale"
                                                      checked="{{ !is_null($storage) ? ($storage->point_of_sale ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-sale">Точка продаж</x-base.form-switch.label>
                        </x-base.form-switch>

                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-delivery" type="checkbox" name="delivery"
                                                      checked="{{ !is_null($storage) ? ($storage->point_of_delivery ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-delivery">Точка выдачи</x-base.form-switch.label>
                        </x-base.form-switch>
                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-default" type="checkbox" name="default"
                                                      checked="{{ !is_null($storage) ? ($storage->default ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-default">Склад по умолчанию</x-base.form-switch.label>
                        </x-base.form-switch>
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                                {{ \App\Forms\Upload::create('file', isset($storage) ? $storage->photo->getUploadUrl() : '')->placeholder('Для карточек')->show() }}
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
                <div><b>Название Хранилища</b> является обязательным полем.</div>
                <div class="mt-2">Поле <b>Slug</b> (ссылка на категорию) можно не заполнять, тогда оно заполнится автоматически. При заполнении использовать латинский алфавит.</div>
                <div class="mt-2">Рекомендуемое разрешение для <b>картинок</b> в карточку категории 700х700.</div>
                <div class="mt-2"><b>latitude</b> и <b>longitude</b> используются для виджета карты.</div>
                <div class="mt-2">Поле <b>Адрес</b> используется также для отображения на карте виджета.</div>
            </div>
        </div>
    </div>
</div>
