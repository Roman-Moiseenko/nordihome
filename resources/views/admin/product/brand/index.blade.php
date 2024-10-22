@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Бренды
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.brand.create') }}'">Добавить бренд
            </button>
            {{ $brands->links('admin.components.count-paginator') }}
        </div>

        <div class="col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
                <thead>
                <tr>
                    <th class="whitespace-nowrap">ЛОГО</th>
                    <th class="whitespace-nowrap">НАЗВАНИЕ</th>
                    <th class="text-center whitespace-nowrap">ОПИСАНИЕ</th>
                    <th class="text-center whitespace-nowrap">ССЫЛКА</th>
                    <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($brands as $brand)
                    @include('admin.product.brand._list', ['brand' => $brand])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ $brands->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить бренд?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
