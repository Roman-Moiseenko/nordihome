@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Продавцы
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.accounting.trader.create') }}'">Добавить Продавца
            </button>
            {{ $traders->links('admin.components.count-paginator') }}
        </div>

        <div class="col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
                <thead>
                <tr>
                    <th class="whitespace-nowrap">-</th>
                    <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                    <th class="text-center whitespace-nowrap">-</th>
                    <th class="text-center whitespace-nowrap">-</th>
                    <th class="text-center whitespace-nowrap">-</th>
                    <th class="text-center whitespace-nowrap">-</th>
                    <th class="text-center whitespace-nowrap">-</th>
                </tr>
                </thead>
                <tbody>
                @foreach($traders as $trader)
                    @include('admin.accounting.trader._list', ['trader' => $trader])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{ $traders->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить продавца?<br>Этот процесс не может быть отменен.')->show() }}
@endsection

