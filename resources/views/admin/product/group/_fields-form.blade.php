<div class="grid grid-cols-12 gap-4 mt-5">
    <!-- Основные данные -->
    <div class="col-span-12">
        <div class="box">
            <div
                class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                <h2 class="font-medium text-base mr-auto">
                    Внесите данные
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 lg:col-span-4">
                        {{ \App\Forms\Input::create('name',
                            ['placeholder' => 'Название группы',
                            'value' => is_null($group) ? '' : $group->name, 'class' => 'mb-3'])->label('Название группы')->show() }}
                        {{ \App\Forms\TextArea::create('description',
                            ['placeholder' => 'Описание',
                            'value' => is_null($group) ? '' : $group->description])->label('Описание группы')->rows(9)->show() }}
                        {{ \App\Forms\Input::create('slug',
                            ['placeholder' => 'Оставьте пустым для автоматического заполнения',
                            'value' => is_null($group) ? '' : $group->slug, 'class' => 'mt-3'])->label('Ссылка')->show() }}
                        <x-base.form-switch class="mt-3">
                            <x-base.form-switch.input id="checkbox-published" type="checkbox" name="published"
                                                      checked="{{ !is_null($group) ? ($group->published ? 'checked' : '') : '' }}"/>
                            <x-base.form-switch.label for="checkbox-published">Показывать страницу</x-base.form-switch.label>
                        </x-base.form-switch>
                    </div>
                    <div class="col-span-12 lg:col-span-4">
                        <div class="w-52">
                            {{ \App\Forms\Upload::create('file', is_null($group) ? '' : $group->photo->getUploadUrl())->show() }}
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
</div>
