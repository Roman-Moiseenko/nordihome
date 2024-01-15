@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Страницы
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.page.page.create') }}'">Создать страницу
            </button>
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">СТРАНИЦА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЗАГОЛОВОК</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ШАБЛОН</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">МЕНЮ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ОПУБЛИКОВАНА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                    </x-base.table.thead>
                    <x-base.table.tbody>
                @foreach($pages as $page)
                    @include('admin.page.page._list', ['page' => $page])
                @endforeach
                </x-base.table.tbody>
                </x-base.table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить Страницу?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
