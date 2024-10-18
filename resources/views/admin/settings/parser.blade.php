@extends('layouts.side-menu')

@section('subcontent')

    <form method="POST" action="{{ route('admin.setting.update') }}" class="box p-4">
        @method('put')
        @csrf
        <input type="hidden" name="slug" value="parser">
        <div class="grid lg:grid-cols-3 grid-cols-1 divide-x">
            <div class="p-2">
                <!-- 1 столбец -->
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_coefficient',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_coefficient])->
                        label('Внутренний курс злота - коэффициент наценки на стоимость')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery])->
                        label('Минимальная стоимость доставки')->type('number')->show() }}
                </div>

                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('cost_weight',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->cost_weight])->
                        label('Доб.стоимость товара за 1 кг веса (руб.)')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('cost_weight_fragile',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->cost_weight_fragile ])->
                        label('Доб.стоимость товара за 1 кг веса для ХРУПКОГО товара (руб.)')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('cost_sanctioned',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->cost_sanctioned ])->
                        label('Коэффициент наценки за санкционный (%)')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('cost_retail',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->cost_retail ])->
                        label('Коэффициент наценки на розницу')->type('number')->show() }}
                </div>

                <div class="p-4 border-b">
                    {{ \App\Forms\CheckSwitch::create('with_proxy', [
         'placeholder' => 'Через proxy',
         'value' => $parser->with_proxy,
         'class' => '',
         ])->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('proxy_ip',
                        ['placeholder' => '195.20.0.20:8080', 'class' => 'w-full lg:w-100', 'value' => $parser->proxy_ip])->
                        label('Адрес прокси-сервера. Формат записи ip:port')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('proxy_user',
                        ['placeholder' => 'user111:p@Sw0rD', 'class' => 'w-full lg:w-100', 'value' => $parser->proxy_user])->
                        label('Доступ к прокси-серверу. Формат записи логин:пароль')->show() }}
                </div>

            </div>
            <div class="p-2">
                <!-- 2 столбец -->
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_0',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_0])->
                        label('Стоимость доставки за 1 кг при весе от 0 до 5')->type('number')->show() }}
                </div>

                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_1',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_1])->
                        label('Стоимость доставки за 1 кг при весе от 5 до 10')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_2',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_2])->
                        label('Стоимость доставки за 1 кг при весе от 10 до 15')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_3',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_3])->
                        label('Стоимость доставки за 1 кг при весе от 15 до 30')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_4',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_4])->
                        label('Стоимость доставки за 1 кг при весе от 30 до 40')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_5',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_5])->
                        label('Стоимость доставки за 1 кг при весе от 40 до 50')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_6',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_6])->
                        label('Стоимость доставки за 1 кг при весе от 50 до 200')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_7',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_7])->
                        label('Стоимость доставки за 1 кг при весе от 200 до 300')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_8',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_8])->
                        label('Стоимость доставки за 1 кг при весе от 300 до 400')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_9',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_9])->
                        label('Стоимость доставки за 1 кг при весе от 400 до 600')->type('number')->show() }}
                </div>

                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('parser_delivery_10',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $parser->parser_delivery_10])->
                        label('Стоимость доставки за 1 кг при весе от 600  до 9999999')->type('number')->show() }}
                </div>
            </div>
            <div class="p-2">
                <!-- 3 столбец -->

            </div>
        </div>
        <div class="form-group mt-5">
            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
        </div>
    </form>

@endsection
