@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Курсы валют
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.accounting.currency.create') }}'">Добавить Валюту
            </button>
        </div>

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
                <thead>
                <tr>
                    <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                    <th class="text-center whitespace-nowrap">ОБОЗНАЧЕНИЕ</th>
                    <th class="text-center whitespace-nowrap">ТЕКУЩИЙ КУРС</th>
                    <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($currencies as $currency)
                    @include('admin.accounting.currency._list', ['currency' => $currency])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить курс валюты?<br>Этот процесс не может быть отменен.')->show() }}
@endsection

