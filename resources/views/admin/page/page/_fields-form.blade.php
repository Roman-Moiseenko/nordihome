
<div class="grid grid-cols-12 gap-6 mt-5">
    <!-- Основные данные -->
    <div class="intro-y col-span-12">
        <div class="intro-y box">
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
                        ['placeholder' => 'Имя страницы', 'value' => !is_null($page) ? $page->name : ''])
                        ->label('Имя')->show() }}
                        {{ \App\Forms\Input::create('slug',
                        ['placeholder' => 'Slug', 'class' => 'mt-3', 'value' => !is_null($page) ? $page->slug : ''])
                        ->label('Slug')->show() }}

                        <x-base.form-label for="select-parent" class="mt-3">Родительская страница</x-base.form-label>
                        <x-base.tom-select id="select-parent" name="parent_od" class="w-full new-tom-select bg-white tom-select"
                                           data-placeholder="Выберите родителя" >
                            <option value="0"></option>
                            @foreach($pages as $item)
                                <option value="{{ $item->id }}"
                                @if(isset($page))
                                    {{ $page->parent_id == $item->id ? 'selected' : ''}}
                                    @endif
                                >{{ $item->name }}</option>
                            @endforeach
                        </x-base.tom-select>

                        Родительская страница - SELECT<br>
                        {{ \App\Forms\Input::create('title',
                        ['placeholder' => 'Заголовок', 'class' => 'mt-3', 'value' => !is_null($page) ? $page->title : ''])
                        ->label('Заголовок')->show() }}
                        {{ \App\Forms\TextArea::create('description', ['placeholder' => 'Описание',
                        'class' => 'mt-3', 'value' => !is_null($page) ? $page->description : ''])->rows(5)->show() }}

                        {{ \App\Forms\CheckSwitch::create('menu', [
                         'placeholder' => 'Разместить страницу в топ-меню', 'class' => 'mt-3',
                         'value' => $page->menu ?? false,
                         ])->show() }}
                        <x-base.form-label for="select-template" class="mt-3">Шаблон</x-base.form-label>
                        <x-base.tom-select id="select-template" name="template" class="w-full new-tom-select bg-white tom-select"
                                data-placeholder="Выберите шаблон" >
                            <option value="0"></option>
                            @foreach($templates as $template)
                                <option value="{{ $template }}"
                                @if(isset($page))
                                    {{ $template == $page->template ? 'selected' : ''}}
                                    @endif
                                >{{ $template }}</option>
                            @endforeach
                        </x-base.tom-select>


                    </div>
                    <div class="col-span-12 lg:col-span-4">

                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
</div>

