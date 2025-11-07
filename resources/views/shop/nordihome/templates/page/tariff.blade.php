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
    <div class="tariff-block">
        <div class="container-xl">
            <div class="row">
                <div class="col-lg-8>
                        <div class="heading-border">Условия доставки</div>
                        <p>
                            Заказ формируется на основе артикулов из каталога ИКЕА
                        </p>
                        <p>
                            Чем больше вес заказа, тем дешевле доставка
                            От 60 рублей за килограмм
                        </p>
                        <p>
                            Срок доставки от 10 дней
                        </p>
                        <p>
                            Оплата 100% на банковский счет организации по договору.
                            Предоставляем кассовый чек об оплате с товарами и услугами
                        </p>
                        <div class="mt-3">
                        <a href="{{ route('shop.parser.view') }}" class="btn btn-gold rounded-pill fs-8 px-4 py-3 text-white">СДЕЛАТЬ ЗАКАЗ</a>
                        </div>

                </div>
                <div class="col-lg-4 pe-4">
                    <img src="/images/pages/tariff.jpg" style="width: 100%" alt="Условия интернет магазина NORDI HOME">
                </div>
            </div>

            <div class="text-center w-100 text-white fs-4 mt-5 pt-4 mb-2">ТАРИФЫ</div>
            <div class="row">
                <div class="col-lg-4 px-3">
                    <div class="heading">ТАРИФЫ НА ДОСТАВКУ ИЗ ПОЛЬШИ</div>
                    <div class="text-center" style="color: var(--bs-gray-100);">МИНИМАЛЬНАЯ СТОИМОСТЬ ДОСТАВКИ 1 000 РУБ<br>стоимость за 1 кг</div>
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
                    <div style="color: var(--bs-gold);">ДЛЯ ОПТОВЫХ ЗАКАЗЧИКОВ<br>ИНДИВИДУАЛЬНЫЕ УСЛОВИЯ</div>
                </div>
                <div class="col-lg-4 px-3">
                    <div class="heading">ДОСТАВКА ПО ГОРОДУ И ОБЛАСТИ</div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>КАЛИНИНГРАД</div>
                            <div>700₽</div>
                        </div>
                        <div class="item">
                            <div>до 10 кг(+подъем)</div>
                            <div>700₽</div>
                        </div>
                        <div class="item m-t_20">
                            <div>СВЕТЛЫЙ</div>
                            <div>1200₽</div>
                        </div>
                        <div class="item">
                            <div>ЗЕЛЕНОГРАДСК</div>
                            <div>1400₽</div>
                        </div>
                        <div class="item">
                            <div>СВЕТЛОГОРСК</div>
                            <div>1800₽</div>
                        </div>
                        <div class="item">
                            <div>БАЛТИЙСК</div>
                            <div>1800₽</div>
                        </div>
                        <div class="item">
                            <div>ПИОНЕРСК</div>
                            <div>1800₽</div>
                        </div>
                        <div class="item">
                            <div>ЯНТАРНЫЙ</div>
                            <div>1800₽</div>
                        </div>
                        <div class="item">
                            <div>ГУРЬЕВСК/ИСАКОВО</div>
                            <div>1100₽</div>
                        </div>
                        <div class="item">
                            <div>ПОС. А. КОСМОДЕМЬЯНСКОГО</div>
                            <div>1100₽</div>
                        </div>
                        <div class="item m-t_20">
                            <div>МАМОНОВО</div>
                            <div>1800₽</div>
                        </div>
                        <div class="item">
                            <div>ГВАРДЕЙСК</div>
                            <div>2000₽</div>
                        </div>
                        <div class="item">
                            <div>ПРАВДИНСК</div>
                            <div>2200₽</div>
                        </div>
                        <div class="item">
                            <div>ЧЕРНЯХОВСК</div>
                            <div>2800₽</div>
                        </div>
                        <div class="item">
                            <div>ГУСЕВ</div>
                            <div>3200₽</div>
                        </div>
                        <div class="item">
                            <div>СОВЕТСК</div>
                            <div>3900₽</div>
                        </div>
                        <div class="item m-t_20">
                            <div style="color: var(--bs-gold);">от 200 кг</div>
                            <div style="color: var(--bs-gold);">+1000₽</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 px-3">
                    <div class="heading">ТАРИФЫ НА ОТПРАВКУ В РФ</div>
                    <div class="text-center" style="color: var(--bs-gray-100);">МИНИМАЛЬНАЯ СТОИМОСТЬ ОТПРАВКИ 800 РУБ<br>ориентировочная стоимость</div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>10 КГ</div>
                            <div>от 2200₽</div>
                        </div>
                        <div class="item">
                            <div>20 КГ</div>
                            <div>от 5200₽</div>
                        </div>
                        <div class="item">
                            <div>30 КГ</div>
                            <div>от 7000₽</div>
                        </div>
                        <div class="item">
                            <div>Возможны корректировки стоимости в зависимости от места назначения и способа доставки</div>
                            <div></div>
                        </div>
                        <div class="item">
                            <div>Страховка отправления</div>
                            <div>от 5%</div>
                        </div>
                    </div>
                    <div class="my-2"  style="color: var(--bs-gold);">
                        Отправления свыше 30 кг и больше 220 см по индивидуальному тарифу, связаться с нами
                        можно на странице <a href="{{ route('shop.page.view', 'contact') }}" style="color: var(--bs-gold);">КОНТАКТЫ</a></div>
                    <div class="heading">ДОПОЛНИТЕЛЬНЫЕ УСЛУГИ</div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>СБОРКА МЕБЕЛИ</div>
                            <div>15%, не менее 600₽</div>
                        </div>
                    </div>
                    <div class="tariffs-items">
                        <div class="item">
                            <div>ДОСТАВКА К ТОЧНОМУ ВРЕМЕНИ</div>
                            <div>500₽</div>
                        </div>
                    </div>
                    <div class="mt-2" style="color: var(--bs-gray-100);">ЗАНОС В КВАРТИРУ:</div>
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
                    <div class="mt-1"  style="color: var(--bs-gold);">МИНИМАЛЬНАЯ СТОИМОСТЬ<br>ЗАНОСА - 500₽</div>
                </div>
            </div>
        </div>
    </div>

    <livewire:shop.widget.feedback />

    <div class="mt-5">
        @include('shop.nordihome.widgets.map')
    </div>
@endsection


