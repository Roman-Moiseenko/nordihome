@extends('layouts.side-menu')

@section('subcontent')

    <form method="POST" action="{{ route('admin.setting.update') }}" class="box p-4">
        @method('put')
        @csrf
        <input type="hidden" name="slug" value="coupon">
        <div class="grid lg:grid-cols-3 grid-cols-1 divide-x">
            <div class="p-2">
                <!-- 1 столбец -->
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('coupon',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $coupon->coupon])->
                        label('Максимальная скидка в %% от сумы заказа')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('coupon_first_bonus',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $coupon->coupon_first_bonus])->
                        label('Сумма в рублях на первую скидку при регистрации')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('coupon_first_time',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $coupon->coupon_first_time])->
                        label('Сколько действует первый купон на покупку (в днях)')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\CheckSwitch::create('bonus_review', [
                         'placeholder' => 'Включить бонусный купон за каждый отзыв при покупке',
                         'value' => $coupon->bonus_review,
                         'class' => '',
                         ])->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('bonus_amount',
                        ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $coupon->bonus_amount])->
                        label('Награждение в рублях за каждый отзыв')->type('number')->show() }}
                </div>
                <div class="p-4 border-b">
                    {{ \App\Forms\Input::create('bonus_discount_delay',
                    ['placeholder' => '', 'class' => 'w-full lg:w-100', 'value' => $coupon->bonus_discount_delay])->
                    label('Время отправления запроса на отзыв после завершения заказа (в днях)')->type('number')->show() }}
                </div>
            </div>
            <div class="p-2">
                <!-- 2 столбец -->

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
