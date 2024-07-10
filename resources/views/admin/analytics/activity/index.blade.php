@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Действия сотрудников
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            {{ $activities->links('admin.components.count-paginator') }}
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-40 whitespace-nowrap">ДАТА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СОТРУДНИК</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ACTION</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ССЫЛКА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ПАРАМЕТРЫ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($activities as $activity)
                        @include('admin.analytics.activity._list', ['activity' => $activity])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $activities->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
