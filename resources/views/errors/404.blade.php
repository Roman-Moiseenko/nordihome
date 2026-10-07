@extends('layouts.blank')
@section('title', 'Страница 404')
@section('main', 'error')
@section('content')

    <div class="container-xl mt-5">
        <h1>Страница не найдена. Ошибка 404</h1>
        <div class="row align-items-center">
            <div class="col-lg-6 t-a_center">
                <img src="/images/nordihome/404-img-min.png" alt="Nordihome мебель из ИКЕА">
            </div>
            <div class="col-lg-5">
                <h3 class="heading m-b_20">Ой, что-то пошло не так...</h3>
                <div class="m-b_20 f-z_17">Мы потеряли эту страницу, возможно, она была удалена с нашего сайта.<br>Пожалуйста, не уходите: воспользуйтесь поиском или перейдите в наш каталог.</div>
                <div><section id="block-13" class="widget widget_block"></section></div>
                <div class="d-flex justify-content-between">
                    <div><a href="/" class="btn btn-white-b t-t_uppercase m-t_20">На главную</a></div>
                    <div><a href="/shop/" class="btn btn-orange m-t_20">Смотреть каталог</a></div>
                </div>
            </div>
        </div>
    </div>


@endsection
