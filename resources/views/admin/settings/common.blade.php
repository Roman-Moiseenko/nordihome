@extends('layouts.side-menu')

@section('subcontent')

    <form method="POST" action="{{ route('admin.setting.update') }}" class="box p-4">
        @method('put')
        @csrf
        <input type="hidden" name="slug" value="common">
        <div class="grid lg:grid-cols-3 grid-cols-1 divide-x">
            <div class="p-2">
                <!-- 1 столбец -->
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('reserve',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $common->reserve])->
                        label('Время резерва товара в минутах')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                {{ \App\Forms\CheckSwitch::create('pre_order', [
                     'placeholder' => 'Возможность оформлять предзаказ, когда товара нет в наличии',
                     'value' => $common->pre_order,
                     'class' => '',
                     ])->show() }}
                </div>
                <div class="p-4 border-b">
                {{ \App\Forms\CheckSwitch::create('only_offline', [
     'placeholder' => 'Продажа товаров только оффлайн, ИМ недоступен',
     'value' => $common->only_offline,
     'class' => '',
     ])->show() }}
                </div>
                <div class="p-4 border-b">
                {{ \App\Forms\CheckSwitch::create('delivery_local', [
     'placeholder' => 'Осуществляется ли доставка товаров по региону собственными силами',
     'value' => $common->delivery_local,
     'class' => '',
     ])->show() }}
                </div>
                <div class="p-4 border-b">
                {{ \App\Forms\CheckSwitch::create('delivery_all', [
     'placeholder' => 'Осуществляется ли доставка товара Транспортными компаниями',
     'value' => $common->delivery_all,
     'class' => '',
     ])->show() }}
                </div>
                <div class="p-4 border-b">
                {{ \App\Forms\CheckSwitch::create('accounting', [
     'placeholder' => 'Поступление товаров только через приходные документы',
     'value' => $common->accounting,
     'class' => '',
     ])->show() }}
                </div>
            </div>
            <div class="p-2">
                <!-- 2 столбец -->

            </div>
            <div class="p-2">
                <!-- 3 столбец -->
                <div class="p-4 border-b">
                {{ \App\Forms\Select::create('group_last_id', ['placeholder' => 'Группа Последний шанс', 'value' => $common->group_last_id ])
                ->options($groups)->label('Группа в которую переносятся остатки товаров снятых с продажи')->show() }}
                </div>
            </div>
        </div>
        <div class="form-group mt-5">
            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
        </div>
    </form>

@endsection
