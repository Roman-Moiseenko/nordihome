@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-5">
            <h1 class="text-lg font-medium mr-auto">
                {{ $currency->name }}
            </h1>
        </div>
    </div>

    <div class="box px-5 py-5 mt-5">
        <!-- Управление -->
        <div class="my-2">
            <h2>Установленный курс</h2>
            <h3 class="mt-2">{{ '1 ' . $currency->sign . ' = ' . $currency->exchange . ' ₽'}}</h3>
        </div>
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.accounting.currency.edit', $currency) }}'">Редактировать
            </button>


@endsection
