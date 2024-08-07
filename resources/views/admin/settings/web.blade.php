@extends('layouts.side-menu')

@section('subcontent')

    <form method="POST" action="{{ route('admin.setting.update') }}" class="box p-4">
        @method('put')
        @csrf
        <input type="hidden" name="slug" value="web">
        <div class="grid lg:grid-cols-3 grid-cols-1 divide-x">
            <div class="p-2">
                <!-- 1 столбец -->
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('paginate',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $web->paginate])->
                        label('Количество товаров на странице')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('logo_img',
                        ['placeholder' => 'Ссылка без тек.домена', 'class' => 'w-full lg:w-100', 'value' => $web->logo_img])->
                        label('Логотип для сайта, с прозрачным фоном (svg, png)')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('logo_alt',
                        ['placeholder' => 'Бренд, магазин или компания', 'class' => 'w-full lg:w-100', 'value' => $web->logo_alt])->
                        label('Подпись (alt) под логотипом')->show() }}
                </div>

            </div>
            <div class="p-2">
                <!-- 2 столбец -->

            </div>
            <div class="p-2">
                <h2 class="font-medium">SEO-настройки (Заголовки)</h2>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('categories_title',
                        ['placeholder' => 'SEO-текст', 'class' => 'w-full lg:w-100', 'value' => $web->categories_title])->
                        label('meta-Title для списка категорий')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('categories_desc',
                        ['placeholder' => 'SEO-текст', 'class' => 'w-full lg:w-100', 'value' => $web->categories_desc])->
                        label('meta-Description для списка категорий')->show() }}
                </div>

                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('title_contact',
                        ['placeholder' => '[+8 800 00 000 00000] (Круглосуточно)', 'class' => 'w-full lg:w-100', 'value' => $web->title_contact])->
                        label('meta-Title Контактные данные')->show() }}
                </div>

                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('title_city',
                        ['placeholder' => 'Coca-Cola Москва', 'class' => 'w-full lg:w-100', 'value' => $web->title_city])->
                        label('meta-Title Бренд и Город')->show() }}
                </div>
            </div>
        </div>
        <div class="form-group mt-5">
            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
        </div>
    </form>

@endsection
