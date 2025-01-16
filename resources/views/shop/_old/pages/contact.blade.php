@extends('layouts.shop')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')

    <div class="container-xl">
        <h1>{{ $page->name }}</h1>
        <div class="row mt-4">
            <div class="col-lg-6 ps-md-4" style="display: grid">
                <div class="about-block">
                    <div class="heading-border">
                        О КОМПАНИИ
                    </div>
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
            <div class="col-lg-6 pe-md-4">
                <a href="{{ route('shop.category.index') }}" >
                    <img src="/images/pages/about.jpg" style="width: 100%" alt="Каталог интернет магазина NORDI HOME">
                </a>
            </div>
        </div>
    </div>

    @include('shop.widgets.contact')

    <div class="mt-5">
        @include('shop.widgets.map')
    </div>
@endsection
