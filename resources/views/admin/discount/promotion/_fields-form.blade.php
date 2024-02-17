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
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Внутреннее имя', 'value' => !is_null($promotion) ? $promotion->name : ''])->show() }}
                        {{ \App\Forms\Input::create('slug', ['placeholder' => 'Slug', 'value' => !is_null($promotion) ? $promotion->slug : '', 'class' => 'mt-3'])->show() }}
                        {{ \App\Forms\CheckSwitch::create('menu', [
                                 'placeholder' => 'Показывать в меню',
                                 'value' => (!is_null($promotion) ? $promotion->menu : ''),
                                 'class' => 'mt-3'
                                 ])->show() }}
                        {{ \App\Forms\Input::create('title', [
                                 'placeholder' => 'Заголовок для клиентов',
                                 'value' => !is_null($promotion) ? $promotion->title : '',
                                 'class' => 'mt-3'
                                 ])->show() }}
                        {{ \App\Forms\Input::create('discount', [
                                 'placeholder' => 'Базовая скидка в %%',
                                 'value' => !is_null($promotion) ? $promotion->discount : '',
                                 'class' => 'mt-3'
                                 ])->show() }}
                        {{ \App\Forms\CheckSwitch::create('show_title', [
                                 'placeholder' => 'Показывать заголовок на карточках',
                                 'value' => (!is_null($promotion) ? $promotion->show_title : ''),
                                 'class' => 'mt-3'
                                 ])->show() }}
                        {{ \App\Forms\Input::create('condition_url', [
                                'placeholder' => 'Ссылка на условия акции',
                                'value' => !is_null($promotion) ? $promotion->condition_url : '',
                                'class' => 'mt-3'
                                ])->show() }}
                        {{ \App\Forms\DatePicker::create('start', [
                                'placeholder' => 'Укажите день старта акции',
                                'value' => !is_null($promotion) ? $promotion->start_at : '',
                                'class' => 'mt-3'])->label('Начало акции')->show() }}
                        {{ \App\Forms\DatePicker::create('finish', [
                                'placeholder' => 'Укажите день окончания акции',
                                'value' => !is_null($promotion) ? $promotion->finish_at : '',
                                'class' => 'mt-3'])->label('Конец акции')->show() }}
                    </div>
                    <div class="col-span-12 lg:col-span-2">
                        {{ \App\Forms\TextArea::create('description', ['placeholder' => 'Описание'])->rows(20)->show() }}
                    </div>
                    <div class="col-span-12 lg:col-span-6">
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-12 lg:col-span-6">
                                {{ \App\Forms\Upload::create('image', !is_null($promotion) ? $promotion->image->getUploadUrl() : '')->placeholder('Для карточек')->show() }}
                            </div>
                            <div class="col-span-12 lg:col-span-6">
                                {{ \App\Forms\Upload::create('icon', !is_null($promotion) ? $promotion->icon->getUploadUrl() : '')->placeholder('Иконка для меню')->show() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 lg:col-span-4 p-5">
                <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
            </div>
        </div>
    </div>
</div>
