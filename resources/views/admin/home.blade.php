@extends('layouts.side-menu')

@section('subcontent')

    @php
        $items = [

            'Парсер теперь работает через прокси, в том числе отложенная загрузка всех изображений. - 23.06.2024',
            'Добавлена форма ТОРГ-12 для Распоряжений. Форма доступна при любом статусе распоряжения - 22.06.2024',
            'Добавлено редактирование Организаций',
   ];
    @endphp

    <h1 class="text-lg mb-5">Режим тестирования</h1>
    <div class="box p-3">
        <h2 class="text-base font-medium mb-2">Последние изменения:</h2>
        <ul>
            @foreach($items as $item)
                <li><span class="circle green"></span>{{ $item }}</li>
            @endforeach
        </ul>
    </div>
@endsection
