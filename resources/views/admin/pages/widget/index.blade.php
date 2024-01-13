@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Виджеты
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.pages.widget.create') }}'">Добавить виджет
            </button>
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ВИДЖЕТ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ОБЪЕКТ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">НАЗВАНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ШАБЛОН</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">АКТИВЕН</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                    </x-base.table.thead>
                    <x-base.table.tbody>
                @foreach($widgets as $widget)
                    @include('admin.pages.widget._list', ['widget' => $widget])
                @endforeach
                </x-base.table.tbody>
                </x-base.table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить виджет?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
