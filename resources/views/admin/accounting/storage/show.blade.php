@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $storage->name }}
            </h1>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.accounting.storage.edit', $storage) }}'">Редактировать
            </button>
            {{ $items->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ИКОНКА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">КАТЕГОРИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ФАКТИЧЕСКИ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">В ДВИЖЕНИИ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">В РЕЗЕРВЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДОСТУПНО ВСЕГО</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($items as $item)
                        @include('admin.accounting.storage._list_items', ['item' => $item])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $items->links('admin.components.paginator', ['pagination' => $pagination]) }}


@endsection
