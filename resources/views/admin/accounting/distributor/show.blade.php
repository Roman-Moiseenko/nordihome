@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $distributor->name }}
            </h1>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.accounting.distributor.edit', $distributor) }}'">
                Редактировать
            </button>
            {{ $items->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЗАКУПОЧНАЯ ЦЕНА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">НАЛИЧИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЦЕНА ПРОДАЖИ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($items as $item)
                        @include('admin.accounting.distributor._item', ['item' => $item])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    {{ $items->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
