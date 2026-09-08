<!--template:Страница Кухни Икеа под заказ-->
@extends('layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
    </div>
        <div class="mt-4">
            {!! $page->text !!}
        </div>
    <div class="parser-fos p-t_50 p-b_50">
        <div class="container">
            <div class="t-t_uppercase f-z_35 t-a_center f-w_600"><span class="t-color_orange">Получите бесплатную консультацию</span><br>по подбору кухни уже сегодня!</div>
            <div class="m-t_10 m-b_20">Мы готовы ответить на Ваши вопросы и помочь с выбором кухни мечты. Заполните форму ниже, и наш менеджер перезвонит Вам в ближайшее время.</div>
            <div id="form-kitches-bottom" class="feedback" not-hide>
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <label>
                            <input name="name" type="text" required placeholder="Имя и Фамилия"/>
                        </label>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label>
                            <input name="phone" type="tel" required placeholder="Ваш телефон: +79097589135"/>
                        </label>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <label>
                            <input name="telegram" placeholder="Ник в Телеграм: @username"/>
                        </label>
                    </div>
                    <div class="col-12"><label class="fos-sert-lable-gray f-z_13">Выберите удобный способ для связи с Вами
                            <span class="wpcf7-form-control-wrap" data-name="menu-487"><select class="wpcf7-form-control wpcf7-select width_100" aria-invalid="false" name="menu-487"><option value="">—Выберите вариант—</option><option value="Позвонить по телефону">Позвонить по телефону</option><option value="Написать в Телеграм">Написать в Телеграм</option><option value="Написать в Макс">Написать в Макс</option></select></span>
                        </label></div>
                    <div class="col-md-9 col-lg-8">
                        <label>
                            <textarea placeholder="Опишите Ваш вопрос или оставьте это поле пустым"></textarea>
                        </label>
                    </div>
                    <div class="col-md-3 col-lg-4">
                        <label><button class="btn-form" type="button">Отправить</button></label>
                    </div>
                    <div class="col-12">
                        <label class="f-z_14">
                            <input type="checkbox" name="agreement" required value="Согласие на обработку персональных данных"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                        </label>
                    </div>
                </div>
            </div>
            <div id="form-kitches-bottom-callback" class="form-send-message" style="display: none">
                Спасибо за Ваше сообщение. Оно успешно отправлено. Наш менеджер свяжется с Вами в ближайшее время.
            </div>
        </div>
    </div>
@endsection

