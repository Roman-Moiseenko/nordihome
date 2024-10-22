@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Акции
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.discount.promotion.create') }}'">Создать акцию
            </button>
            {{ $promotions->links('admin.components.count-paginator') }}
        </div>

        <div class="col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">IMG</th>
                <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                <th class="text-center whitespace-nowrap">НАЧАЛО - КОНЕЦ</th>
                <th class="text-center whitespace-nowrap">СТАТУС</th>
                <th class="text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($promotions as $promotion)
                @include('admin.discount.promotion._list', ['promotion' => $promotion])
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{ $promotions->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить акцию?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
