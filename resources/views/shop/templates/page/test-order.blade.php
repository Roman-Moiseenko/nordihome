<!--template:Страница оформленного заказа-->
@php
    use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
    /** @var PageViewPageData $pageData */
@endphp
@extends('layouts.main')
@section('main', 'pages order-page-create')

@section('content')
    <div class="container-xl">
        <h1 class="my-4">Спасибо! Ваш заказ принят</h1>
    </div>
    <div class="container-xl">
        <div class="row">
            <div class="col-md-12 col-lg-7 m-b_20">
                <div class="m-b_30">Менеджер свяжется с Вами в часы работы магазина.</div>
                <div class="b-radius_12 border_black d-flex m-b_30 new-order-info">
                    <div>
                        <div class="f-z_14">Номер заказа</div>
                        <div class="f-z_23 f-w_600">№ 40957</div>
                    </div>
                    <div>
                        <div class="f-z_14">Дата заказа</div>
                        <div class="f-z_23 f-w_600">06.10.2026</div>
                    </div>
                    <div>
                        <div class="f-z_14">Сумма заказа</div>
                        <div class="f-z_23 f-w_600">29 290 ₽</div>
                    </div>
                </div>
                <div class="new-order-time bg-light-orange b-radius_12">
                    <div class="f-w_600"><svg width="40" height="39" viewBox="0 0 40 39" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.8683 13V19.5L23.1797 22.75M19.8683 4.875C11.6384 4.875 4.9668 11.4228 4.9668 19.5C4.9668 27.5772 11.6384 34.125 19.8683 34.125C28.0982 34.125 34.7697 27.5772 34.7697 19.5C34.7697 16.5098 33.8554 13.7293 32.2861 11.4129" stroke-width="2.3125" stroke-linecap="round" stroke-linejoin="round" stroke="black"/>
                        </svg> Магазин работает по следующему графику:
                    </div>
                    <div class="m-t_20 b-radius_12 bg-white">
                        <table class="f-w_600">
                            <tr class="f-z_14">
                                <td></td>
                                <td>по МСК:</td>
                                <td>по КЛД:</td>
                            </tr>
                            <tr>
                                <td>ПН - ПТ</td>
                                <td>11:00-20:00</td>
                                <td>10:00-19:00</td>
                            </tr>
                            <tr>
                                <td>СБ - ВС</td>
                                <td>12:00-19:00</td>
                                <td>11:00-18:00</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block"><img src="/images/new-order/page-new-order01-min.jpg" alt="Ващ заказ в Норди хоум принят"></div>
        </div>
        <div class="order-table-items">
            <h3>Состав заказа (2 товара) ДОДЕЛАТЬ ТАБЛИЧКУ</h3>
            <div class="order-table-product f-z_16">
                <div class="image"></div>
                <div class="info">Наименование</div>
                <div class="count">Количество</div>
                <div class="price">Цена</div>
            </div>
            <div class="order-table-product">
                <div class="image"><img src="/images/no-image.jpg" alt="Название товара" class="b-radius_8"></div>
                <div class="info">
                    <div class="m-b_5"><a href="/">Модульная кухня, темно-серая, 180x61 см КНОКСХУЛЬТ (KNOXHULT) ИКЕА</a></div>
                    <div class="f-z_16">Артикул: 296.149.37</div>
                </div>
                <div class="count">1 шт</div>
                <div class="f-w_600 price">29 290 ₽</div>
            </div>
            <div class="order-table-product">
                <div class="image"><img src="/images/no-image.jpg" alt="Название товара" class="b-radius_8"></div>
                <div class="info">
                    <div class="m-b_5"><a href="/">Модульная кухня, темно-серая, 180x61 см КНОКСХУЛЬТ (KNOXHULT) ИКЕА</a></div>
                    <div class="f-z_16">Артикул: 296.149.37</div>
                </div>
                <div class="count">1 шт</div>
                <div class="f-w_600 price">29 290 ₽</div>
            </div>
            <div class="order-table-full">
                <div class="order-table-full-item m-b_20">
                    <div>Итого:</div>
                    <div><b>58 580 ₽</b></div>
                </div>
                <div class="order-table-full-item">
                    <div>Доставка:</div>
                    <div><b>-</b></div>
                </div>
            </div>
            <div class="order-table-total">
                <div class="f-z_23 f-w_600">Итого: 58 580 ₽</div>
            </div>
        </div>
        <div class="m-t_30 m-b_30 t-a_center">
            <a href="/catalog/" class="btn btn-black m-r_10 m-b_10"><svg width="27" height="27" viewBox="0 0 27 27" fill="none" class="m-r_10" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.4167 18.5833L8.25 13.4167L13.4167 8.25M8.25 13.4167H18.5833M26.3333 13.4167C26.3333 20.5503 20.5503 26.3333 13.4167 26.3333C6.28299 26.3333 0.5 20.5503 0.5 13.4167C0.5 6.28299 6.28299 0.5 13.4167 0.5C20.5503 0.5 26.3333 6.28299 26.3333 13.4167Z" stroke-linecap="round" stroke-linejoin="round" stroke="white"/>
                </svg> Вернуться в каталог</a> <a href="/" class="btn btn-white-b m-b_10">На Главную</a>
        </div>
    </div>
    <div class="stay-informed bg-light-orange_1">
        <div class="container-xl">
                <div class="t-a_center d-block d-lg-none"><img src="/images/new-order/page-new-order03.png" alt="Будьте вкурсе всех событий магазина НОРДИ ХОУМ"></div>
                <div class="row">
                    <div class="col-lg-6 col-xl-5 col-xxl-4">
                        <div class="text">
                            <h3>Будьте вкурсе всех событий магазина НОРДИ ХОУМ</h3>
                            <div>Подпишитесь на наши соцсети, чтобы не пропустить акции и поступление новинок в нашем каталоге.</div>
                            <div class="stay-informed-social m-t_20">
                                @if(isset($contacts['vk']))
                                    <a href="{{ $contacts['vk']->url }}" class="link" target="_blank"
                                       data-analytics-action="contact_click"
                                       data-analytics-payload='{"channel":"{{ $contacts['vk']->channel }}","placement":"contacts-block"}'
                                    >{!! $contacts['vk_1']->svg !!}</a>
                                @endif
                                @if(isset($contacts['telegram']))
                                        <a href="{{ $contacts['telegram']->url }}" class="link" target="_blank"
                                           data-analytics-action="contact_click"
                                           data-analytics-payload='{"channel":"{{ $contacts['telegram']->channel }}","placement":"contacts-block"}'
                                        >{!! $contacts['telegram_bot']->svg !!}</a>
                                @endif
                                @if(isset($contacts['yandex_ritm']))
                                        <a href="{{ $contacts['yandex_ritm']->url }}" class="link" target="_blank"
                                           data-analytics-action="contact_click"
                                           data-analytics-payload='{"channel":"{{ $contacts['yandex_ritm']->channel }}","placement":"contacts-block"}'
                                        >{!! $contacts['yandex_ritm']->svg !!}</a>
                                @endif
                                @if(isset($contacts['max_group']))
                                        <a href="{{ $contacts['max_group']->url }}" class="link" target="_blank"
                                           data-analytics-action="contact_click"
                                           data-analytics-payload='{"channel":"{{ $contacts['max_group']->channel }}","placement":"contacts-block"}'
                                        >{!! $contacts['max_bot']->svg !!}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    <div class="bg-e5e5e5 p-block_50">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-4">
                    <div class="f-w_600 m-b_10">Подпишитесь на нашу email-рассылку, чтобы получать специальные предложения и больше идей по обустройству дома</div>
                </div>
                <div class="col-lg-8">
                    <div id="page-new-order" class="feedback-form">
                        <div class="row">
                            <div class="col-md-8 col-lg-8">
                                <div>
                                    <label>
                                        <input name="mail" type="email" class="width_100" required placeholder="Введите Ваш E-mail*"/>
                                    </label>
                                </div>
                                <div class="m-t_10">
                                    <label class="f-z_14 t-color_dark-gray">
                                        <input  type="checkbox" required name="agreement" value="Принимаю согласие"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных и получение персональных рассылок. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-4"><label><button class="btn-form btn btn-orange width_100" type="button">Подписаться</button></label></div>
                        </div>
                    </div>
                    <div id="page-new-order-callback" class="form-send-message" style="display: none">
                        Вы подписались на рассылку.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

