
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


                        <x-base.form-label for="select-id" class="mt-3">Шаблон</x-base.form-label>
                        <select id="select-template" name="template" class="w-full new-tom-select bg-white tom-select"
                                data-placeholder="Выберите шаблон" >
                            <option value="0"></option>
                            @foreach($templates as $template => $name)
                                <option value="{{ $template }}"
                                @if(isset($page))
                                    {{ $template == $page->template ? 'selected' : ''}}
                                    @endif
                                >{{ $name }}</option>
                            @endforeach
                        </select>


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

