@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Скидки
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.discount.discount.create') }}'">Добавить скидку
            </button>

        </div>

        <div class="col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                <th class="whitespace-nowrap">СКИДКА</th>
                <th class="text-center whitespace-nowrap">УСЛОВИЕ</th>
                <th class="text-center whitespace-nowrap">ТИП</th>
                <th class="text-center whitespace-nowrap">АКТИВНА</th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($discounts as $discount)
                @include('admin.discount.discount._list', ['discount' => $discount])
            @endforeach
            </tbody>
        </table>
        </div>
    </div>


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить скидку?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
