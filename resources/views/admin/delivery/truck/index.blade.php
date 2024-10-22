@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Транспорт компании
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.delivery.truck.create') }}'">Добавить транспорт
            </button>

            {{ $trucks->links('admin.components.count-paginator') }}

        </div>
        <div class="col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                <th class="text-center whitespace-nowrap">ГРУЗОПОДЪЕМНОСТЬ</th>
                <th class="text-center whitespace-nowrap">ОБЪЕМ</th>
                <th class="text-center whitespace-nowrap">ВОДИТЕЛЬ</th>
                <th class="text-center whitespace-nowrap">АКТИВЕН</th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($trucks as $truck)
                @include('admin.delivery.truck._list', ['truck' => $truck])
            @endforeach
            </tbody>
        </table>
        </div>
    </div>

    {{ $trucks->links('admin.components.paginator', ['pagination' => $pagination]) }}
@endsection

