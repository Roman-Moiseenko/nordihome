@extends('shop.nordihome.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')

    <div class="container-xl my-3">
        <h1>{{ $page->name }}</h1>
    </div>
    <div class="block-delivery-terms p-t_50 p-b_50 bg-black">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-6">
                    <div class="heading-border">Условия доставки</div>
                    <div class="m-t_50 m-b_50">
                        <p class="m-b_20">Заказ формируется на основе артикулов из <a href="https://www.ikea.com/pl/pl/" target="_blank" class="t-color_orange">каталога ИКЕА</a>, предварительно включите VPN.</p>
                        <p class="m-b_20">Чем больше вес заказа, тем дешевле доставка<br>От 60 рублей за килограмм</p>
                        <p class="m-b_20">Срок доставки от 10 дней</p>
                        <p class="m-b_20">Оплата 100% на банковский счет организации по договору<br>Предоставляем кассовый чек об оплате с товарами и услугами</p>
                    </div>
                    <a href="/calculate/" class="btn btn-big btn-orange f-z_14">Сделать заказ</a>
                </div>
                <div class="col-lg-6">
                    <img src="/wp-content/themes/euroikea/images/poster-with-vertical.jpg" alt="Условия доставки">
                </div>
            </div>
        </div>
    </div>
    <div class="block-tariffs p-t_50 p-b_30 bg-black t-a_center">
        <div class="container">
            <h2 class="page-h2">Тарифы</h2>
            <div class="row">
                <div class="col-lg-4">
                    <div class="heading">ТАРИФЫ НА ДОСТАВКУ ИЗ ПОЛЬШИ</div>
                    <div>МИНИМАЛЬНАЯ СТОИМОСТЬ ДОСТАВКИ 1 000 РУБ<br>стоимость за 1 кг</div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>0-5 КГ</div>
                            <div>180₽</div>
                        </div>
                        <div class="item">
                            <div>5-10 КГ</div>
                            <div>160₽</div>
                        </div>
                        <div class="item">
                            <div>10-15 КГ</div>
                            <div>140₽</div>
                        </div>
                        <div class="item">
                            <div>15-29 КГ</div>
                            <div>105₽</div>
                        </div>
                        <div class="item">
                            <div>30-40 КГ</div>
                            <div>85₽</div>
                        </div>
                        <div class="item">
                            <div>41-50 КГ</div>
                            <div>75₽</div>
                        </div>
                        <div class="item">
                            <div>51-200 КГ</div>
                            <div>70₽</div>
                        </div>
                        <div class="item">
                            <div>201-300 КГ</div>
                            <div>68₽</div>
                        </div>
                        <div class="item">
                            <div>301-400 КГ</div>
                            <div>66₽</div>
                        </div>
                        <div class="item">
                            <div>401-600 КГ</div>
                            <div>63₽</div>
                        </div>
                        <div class="item">
                            <div>601+ КГ</div>
                            <div>60₽</div>
                        </div>
                    </div>
                    <div class="t-color_orange">ДЛЯ ОПТОВЫХ ЗАКАЗЧИКОВ<br>ИНДИВИДУАЛЬНЫЕ УСЛОВИЯ</div>
                </div>
                <div class="col-lg-4">
                    <div class="heading">ДОСТАВКА ПО ГОРОДУ И ОБЛАСТИ</div>
                    <div class="tariffs-items">
                        <div class="item m-t_20">
                            <div class="t-a_left">Доставка в пределах<br>окружной дороги</div>
                            <div>1600₽</div>
                        </div>
                        <div class="item m-t_20">
                            <div>Доставка за город</div>
                            <div>40₽/км.</div>
                        </div>
                        <div class="item">
                            <div>СВЕТЛЫЙ</div>
                            <div>2000₽</div>
                        </div>
                        <div class="item">
                            <div>ЗЕЛЕНОГРАДСК</div>
                            <div>2200₽</div>
                        </div>
                        <div class="item">
                            <div>СВЕТЛОГОРСК</div>
                            <div>2500₽</div>
                        </div>
                        <div class="item">
                            <div>БАЛТИЙСК</div>
                            <div>3000₽</div>
                        </div>
                        <div class="item">
                            <div>ПИОНЕРСКИЙ</div>
                            <div>2500₽</div>
                        </div>
                        <div class="item">
                            <div>ЯНТАРНЫЙ</div>
                            <div>2500₽</div>
                        </div>
                        <div class="item">
                            <div>ГУРЬЕВСК/ИСАКОВО</div>
                            <div>1800₽</div>
                        </div>
                        <div class="item">
                            <div>ПОС. А. КОСМОДЕМЬЯНСКОГО</div>
                            <div>2000₽</div>
                        </div>
                        <div class="item m-t_20">
                            <div>МАМОНОВО</div>
                            <div>2500₽</div>
                        </div>
                        <div class="item">
                            <div>ГВАРДЕЙСК</div>
                            <div>2500₽</div>
                        </div>
                        <div class="item">
                            <div>ПРАВДИНСК</div>
                            <div>2500₽</div>
                        </div>
                        <div class="item">
                            <div>ЧЕРНЯХОВСК</div>
                            <div>3300₽</div>
                        </div>
                        <div class="item">
                            <div>ГУСЕВ</div>
                            <div>3900₽</div>
                        </div>
                        <div class="item">
                            <div>СОВЕТСК</div>
                            <div>4500₽</div>
                        </div>
                        <div class="item">
                            <div>НЕМАН</div>
                            <div>5900₽</div>
                        </div>
                        <div class="item">
                            <div>ПОС. ДОНСКОЕ</div>
                            <div>2800₽</div>
                        </div>
                        <div class="item">
                            <div>ЗАОЗЕРЬЕ</div>
                            <div>2000₽</div>
                        </div>
                        <div class="item">
                            <div>ЧКАЛОВСК</div>
                            <div>2000₽</div>
                        </div>
                        <div class="item">
                            <div>ВАСИЛЬКОВО</div>
                            <div>1600₽</div>
                        </div>
                        <div class="item">
                            <div>ПОЛЕССК</div>
                            <div>3600₽</div>
                        </div>
                        <div class="item">
                            <div>ГОЛУБЕВО</div>
                            <div>2300₽</div>
                        </div>
                        <div class="item">
                            <div>БОРИСОВО</div>
                            <div>2400₽</div>
                        </div>


                        <div class="item m-t_20">
                            <div class="t-color_orange">от 200 кг</div>
                            <div class="t-color_orange">+1000₽</div>
                        </div>
                    </div>
                    <div class="t-color_orange m-t_10">*На малогабаритный груз, за исключением зеркал</div>
                    <div class="t-color_orange m-t_10">Доставка осуществляется в течение дня. Перед приездом с Вами связывается заранее курьер.</div>
                </div>
                <div class="col-lg-4">
                    <div class="heading">ТАРИФЫ НА ОТПРАВКУ В РФ</div>
                    <div>МИНИМАЛЬНАЯ СТОИМОСТЬ ОТПРАВКИ 1000 РУБ<br>ориентировочная стоимость</div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>10 КГ</div>
                            <div>от 4000₽</div>
                        </div>
                        <div class="item">
                            <div>20 КГ</div>
                            <div>от 8000₽</div>
                        </div>
                        <div class="item">
                            <div>30 КГ</div>
                            <div>от 13000₽</div>
                        </div>
                        <div class="item">
                            <div>Возможны корректировки стоимости в зависимости от места назначения и способа доставки</div>
                            <div></div>
                        </div>
                    </div>
                    <div class="t-color_orange m-t_20 m-b_20">Отправления свыше 30 кг или более 150 см рассчитываются по индивидуальному тарифу, как негабаритные грузы.<br>Связаться с нами можно на странице <a href="/kontakty/" class="t-color_orange">КОНТАКТЫ</a></div>
                    <div class="heading">ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ</div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>СБОРКА МЕБЕЛИ</div>
                            <div>15%, не менее 600₽</div>
                        </div>
                    </div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>ДОСТАВКА К ТОЧНОМУ ВРЕМЕНИ*</div>
                            <div>500₽</div>
                        </div>
                        <div class="t-color_orange m-t_10"> *Необходимо указать время в комментарии к заказу или менеджеру по телефону.</div>
                    </div>
                    <div class="m-t_20">ЗАНОС В КВАРТИРУ:</div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>НА ЛИФТЕ ДО 200 КГ</div>
                            <div>500₽</div>
                        </div>
                        <div class="item">
                            <div>НА ЛИФТЕ ДО 500 КГ</div>
                            <div>700₽</div>
                        </div>
                        <div class="item">
                            <div>НА ЛИФТЕ ОТ 500 КГ</div>
                            <div>1000₽</div>
                        </div>
                        <div class="item">
                            <div>ПО ЛЕСТНИЦЕ ДО 200 КГ</div>
                            <div>300₽/ЭТАЖ</div>
                        </div>
                        <div class="item">
                            <div>ПО ЛЕСТНИЦЕ ДО 500 КГ</div>
                            <div>400₽/ЭТАЖ</div>
                        </div>
                        <div class="item">
                            <div>ПО ЛЕСТНИЦЕ ОТ 500 КГ</div>
                            <div>500₽/ЭТАЖ</div>
                        </div>
                    </div>
                    <div class="t-color_orange m-t_10">МИНИМАЛЬНАЯ СТОИМОСТЬ<br>ЗАНОСА - 500₽</div>
                    <div class="t-color_orange m-t_10">*При доставке крупногабаритных товаров добавляется коэффициент сложности 1,5</div>
                </div>
            </div>
        </div>
    </div>
    <div class="block-tariffs p-t_30 p-b_50 bg-black">
        <div class="container">
            <h2 class="page-h2 t-a_center">ГРУЗОВЫЕ АВТОПЕРЕВОЗКИ (без покупки)</h2>
            <div class="row">
                <div class="col-lg-4">
                    <div class="heading t-a_center">ГАБАРИТЫ МАШИНЫ</div>
                    <div class="m-b_10"><b>Машина (тент, бок, лопата):</b></div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>Длина</div>
                            <div>6м</div>
                        </div>
                        <div class="item">
                            <div>Ширина</div>
                            <div>2.2м</div>
                        </div>
                        <div class="item">
                            <div>Высота</div>
                            <div>2.5м</div>
                        </div>
                        <div class="item">
                            <div>Палет</div>
                            <div>12</div>
                        </div>
                        <div class="item">
                            <div>Грузоподъемность</div>
                            <div>5</div>
                        </div>
                    </div>
                    <div class="m-b_10 m-t_10"><b>Машина (будка, лопата):</b></div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>Длина</div>
                            <div>6м</div>
                        </div>
                        <div class="item">
                            <div>Ширина</div>
                            <div>2.4м</div>
                        </div>
                        <div class="item">
                            <div>Высота</div>
                            <div>2.3м</div>
                        </div>
                        <div class="item">
                            <div>Палет</div>
                            <div>13</div>
                        </div>
                        <div class="item">
                            <div>Грузоподъемность</div>
                            <div>5</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="heading t-a_center">ДОСТАВКА ПО ГОРОДУ И ОБЛАСТИ</div>
                    <div class="tariffs-items">
                        <div class="item m-b_10">
                            <div><b>КАЛИНИНГРАД:</b></div>
                            <div></div>
                        </div>
                        <div class="item">
                            <div>Доставка по городу</div>
                            <div>от 1500 в час</div>
                        </div>
                        <div class="item">
                            <div>Минимальный заказ 2,5 часа</div>
                            <div></div>
                        </div>
                        <div class="item m-b_10 m-t_10">
                            <div><b>ОБЛАСТЬ:</b></div>
                            <div></div>
                        </div>
                        <div class="item m-b_10">
                            <div class="t-color_dark-gray f-z_16">в стоимость входит 1 час на загрузку, 1 час на разгрузку и время в дороге (превышение времени на загрузку и разгрузку оплачивается дополнительно)</div>
                            <div></div>
                        </div>
                        <div class="item">
                            <div>Зеленоградск</div>
                            <div>5000₽</div>
                        </div>
                        <div class="item">
                            <div>Сокольники </div>
                            <div>6000₽</div>
                        </div>
                        <div class="item">
                            <div>Куликово</div>
                            <div>6500₽</div>
                        </div>
                        <div class="item">
                            <div>Светлогорск</div>
                            <div>7000₽</div>
                        </div>
                        <div class="item">
                            <div>Донское</div>
                            <div>8000₽</div>
                        </div>
                        <div class="item">
                            <div>Янтарный</div>
                            <div>8000₽</div>
                        </div>
                        <div class="item">
                            <div>Балтийск</div>
                            <div>8000₽</div>
                        </div>
                        <div class="item">
                            <div>Светлый</div>
                            <div>5500₽</div>
                        </div>
                        <div class="item">
                            <div>Взморье</div>
                            <div>4500₽</div>
                        </div>
                        <div class="item">
                            <div>Гвардейск</div>
                            <div>6000₽</div>
                        </div>
                        <div class="item">
                            <div>Знаменск</div>
                            <div>7000₽</div>
                        </div>
                        <div class="item">
                            <div>Черняховск</div>
                            <div>10000₽</div>
                        </div>
                        <div class="item">
                            <div>Гусев</div>
                            <div>14000₽</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="heading t-a_center">ДОСТАВКА ПО ГОРОДУ И ОБЛАСТИ</div>
                    <div class="tariffs-items">
                        <div class="item m-b_10">
                            <div><b>ОБЛАСТЬ:</b></div>
                            <div></div>
                        </div>
                        <div class="item">
                            <div>Советск</div>
                            <div>14000₽</div>
                        </div>
                        <div class="item">
                            <div>Неман</div>
                            <div>14000₽</div>
                        </div>
                        <div class="item">
                            <div>Краснознаменск</div>
                            <div>16500₽</div>
                        </div>
                        <div class="item">
                            <div>Большаково</div>
                            <div>10000₽</div>
                        </div>
                        <div class="item">
                            <div>Полеск</div>
                            <div>6500₽</div>
                        </div>
                        <div class="item">
                            <div>Багратионовск</div>
                            <div>6500₽</div>
                        </div>
                        <div class="item">
                            <div>Мамоново</div>
                            <div>8000₽</div>
                        </div>
                        <div class="item">
                            <div>Прибрежный</div>
                            <div>4500₽</div>
                        </div>
                        <div class="item">
                            <div>Ладушкин</div>
                            <div>6500₽</div>
                        </div>
                        <div class="item">
                            <div>Холмогоровка</div>
                            <div>4000₽</div>
                        </div>
                        <div class="item">
                            <div>Правдинск</div>
                            <div>6500₽</div>
                        </div>
                        <div class="item">
                            <div>Железнодорожный</div>
                            <div>10000₽</div>
                        </div>
                        <div class="item">
                            <div>Луговое</div>
                            <div>4000₽</div>
                        </div>
                        <div class="item">
                            <div>Тишино</div>
                            <div>5000₽</div>
                        </div>
                        <div class="item">
                            <div>Озерки</div>
                            <div>6000₽</div>
                        </div>
                        <div class="item">
                            <div>Комсомольск</div>
                            <div>5000₽</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <livewire:shop.widget.feedback />

    <div class="mt-5">
        @include('shop.nordihome.widgets.map')
    </div>
@endsection


