@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Хранилища (Магазины, Склады, Точки выдачи)
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.accounting.storage.create') }}'">Добавить Хранилище
            </button>
        </div>

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">IMG</th>
                <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                <th class="text-center whitespace-nowrap">АДРЕС</th>
                <th class="text-center whitespace-nowrap">ТОЧКА ПРОДАЖИ</th>
                <th class="text-center whitespace-nowrap">ТОЧКА ВЫДАЧИ</th>
                <th class="text-center whitespace-nowrap">ТОВАРЫ (ВИДЫ)</th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($storages as $storage)
                @include('admin.accounting.storage._list', ['storage' => $storage])
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

@endsection
