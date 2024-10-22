@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Контакты
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.page.contact.create') }}'">Добавить контакт
            </button>
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">КОНТАКТ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ICON</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ССЫЛКА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ОПУБЛИКОВАН</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($contacts as $contact)
                        @include('admin.page.contact._list', ['contact' => $contact])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить Страницу?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
