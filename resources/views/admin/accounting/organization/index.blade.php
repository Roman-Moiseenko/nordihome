@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Организации
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <a href="{{ route('admin.accounting.organization.create') }}" class="btn btn-primary shadow-md mr-2"
                    type="button">Добавить Организацию
            </a>
            {{ $organizations->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ОРГАНИЗАЦИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ИНН</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">РУКОВОДИТЕЛЬ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ПО-УМОЛЧАНИЮ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($organizations as $organization)
                        @include('admin.accounting.organization._list', ['organization' => $organization])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить организацию?<br>Этот процесс не может быть отменен.')->show() }}
    {{ $organizations->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
