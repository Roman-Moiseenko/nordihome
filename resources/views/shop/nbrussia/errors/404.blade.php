@extends('shop.nbrussia.layouts.blank')

@section('body', 'error')
@section('main', 'container-xl 404')
@section('title', 'Страница не найдена')
@section('description', '')
@section('content')
    <div>
        <div style="color: var(--bs-secondary-700); font-size: 240px; font-weight: 600">404</div>
        <div style="font-size: 60px; font-weight: 600;">Такой страницы больше нет</div>

        <div class="mt-5 d-flex items-center">
            <a href="{{ route('shop.category.index') }}" class="btn-nb">Каталог товаров</a>
        </div>
    </div>



@endsection
