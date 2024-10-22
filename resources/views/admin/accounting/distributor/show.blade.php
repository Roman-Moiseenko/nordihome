@extends('layouts.side-menu')

@section('subcontent')

    <x-company.info
        :title="$distributor->name"
        :company="$distributor->organization"
        route="{{ route('admin.accounting.organization.edit', $distributor->organization) }}"
        :show="false"
    />
    <div class="grid grid-cols-12 gap-4 mt-5">
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
    </div>

@endsection
