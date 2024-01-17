@extends('layouts.shop')

@section('main')
    pages
@endsection

@section('content')

    <div class="container-xl">
        <h1>{{ $page->name }}</h1>
        <div class="row mt-4">
            <div class="col-lg-6 ps-4" style="display: grid">
                <div class="about-block">
                    <p class="about-header">
                        О КОМПАНИИ
                    </p>
                    <p>
                        NORDI HOME — Бренд, успешно работающий в Калининграде с 2020 года. Более 7 000 счастливых клиентов!
                        Компания занимается продажей и доставкой мебели ИКЕА из Европы под ключ для вашего удобства. Ниже
                        можно ознакомиться более подробно с условиями и тарифами на доставку
                    </p>
                    <p>
                        Также в <a href="{{ route('shop.category.index') }}">нашем каталоге</a> Вы можете выбрать и купить мебель ИКЕА уже сегодня!
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('shop.category.index') }}" class="btn btn-light rounded-pill">ПЕРЕЙТИ В КАТАЛОГ</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 pe-4">
                <a href="{{ route('shop.category.index') }}" >
                    <img src="/images/pages/about.jpg" style="width: 100%" alt="Каталог интернет магазина NORDI HOME">
                </a>
            </div>
        </div>
    </div>

    <div class="container-xl mt-5">
        <div class="row">
            <div class="col-lg-6">
                <h2>Контакты</h2>
                <div class="">Мы всегда рады ответить на все ваши вопросы,<br>принять пожелания и предложения по работе нашего сервиса</div>
                <div class="my-4 d-flex justify-content-between">
                    <i class="fa-thin fa-phone-office fs-1"></i>
                    <div style="display:flex; margin: auto 0;">
                        <a href="tel:+74012373730" class="fs-4 me-3">+7 (4012) 37-37-30</a>
                        <a href="https://wa.me/+79062108505?text=Здравствуйте!%20Хочу%20мебель%20из%20ИКЕА!" class="me-3">
                            <img src="/images/pages/whatsapp.png" style="width: 100%;">
                        </a>
                        <a href="https://t.me/nordi_home">
                            <img src="/images/pages/telegram.png" style="width: 100%;">
                        </a>
                    </div>
                </div>
                <hr/>
                <div class="my-4 d-flex justify-content-between">
                    <i class="fa-thin fa-planet-ringed fs-1"></i>
                    <div style="display:flex; margin: auto 0;">
                        <a href="https://vk.com/nordihome" class="me-3">
                            <img src="/images/pages/vk.png" style="width: 100%;">
                        </a>
                        <a href="https://www.avito.ru/user/767a54a084b8b382bc26e36a914ec5f7/profile/all?sellerId=767a54a084b8b382bc26e36a914ec5f7">
                            <img src="/images/pages/avito.png" style="width: 100%;">
                        </a>
                    </div>
                </div>
                <hr/>

                <div class="my-4 d-flex justify-content-between">
                    <i class="fa-thin fa-square-envelope fs-1"></i>
                    <div>
                        <a href="mailto:info@nordihome.ru" class="btn btn-outline-dark rounded-pill">info@nordihome.ru</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2>Форма обратной связи</h2>
                <form method="POST" action="{{ route('shop.page.email') }}">
                    @method('PUT')
                    @csrf
                    <div class="form-floating mt-2">
                        <input type="email" class="form-control" name="email" placeholder="Электронная почта" required="" autofocus="autofocus">
                        <label for="email">Электронная почта</label>
                    </div>
                    <div class="form-floating mt-3">
                        <input type="text" class="form-control" name="phone" placeholder="Телефон" required="" >
                        <label for="phone">Телефон</label>
                    </div>
                    <div class="form-floating mt-3">
                        <textarea id="message" class="form-control" name="message" placeholder="Сообщение" required="" rows="8" style="height: 150px"></textarea>
                        <label for="message">Сообщение</label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"  required="">
                        <label class="form-check-label" for="flexCheckDefault">
                            Я согласен на обработку персональных данных
                        </label>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <button id="button-login" type="button" class="btn btn-dark fs-5 py-2 px-3">Отправить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-5">
        @include('shop.widgets.map')
    </div>
@endsection
