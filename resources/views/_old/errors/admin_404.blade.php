@extends('layouts.blank-admin')
@section('title', 'Страница 404')
@section('content')
<div class="error-page flex flex-col lg:flex-row items-center justify-center h-screen text-center lg:text-left bg-gray-600">
    <div class="lg:mr-20">
        <img class="h-48 lg:h-auto" src="/images/error-illustration.svg">
    </div>
    <div class="text-white mt-10 lg:mt-0">
        <div class="text-8xl font-medium">404</div>
        <div class="text-xl lg:text-3xl font-medium mt-5">Ой-ой-ой. Эта страница куда-то пропала.</div>
        <div class="text-lg mt-3">Возможно, вы неправильно ввели адрес или мы спрятали страницу.</div>
        <button class="btn btn-light py-3 px-4 text-white border-white dark:border-darkmode-400 dark:text-slate-200 mt-10"
                onclick="location.href = '/admin'">Вернуть как было</button>
    </div>
</div>
@endsection
