@extends('layouts.side-menu')

@section('subcontent')

    <x-company.info
        :title="$distributor->name"
        :company="$distributor->organization"
        route="{{ route('admin.accounting.organization.edit', $distributor->organization) }}"
        :show="false"
    />
    <div class="flex items-center mt-3">
        <button class="btn btn-success"
                onclick="event.preventDefault();
                document.getElementById('supply-form').submit();"
        >
            Заказать Все
        </button>
        <button class="btn btn-primary ml-2"
                onclick="event.preventDefault();
                document.getElementById('supply-input').value='empty';
                document.getElementById('supply-form').submit();"
        >
            Заказать отсутствующий
        </button>
        <button class="btn btn-outline-primary ml-2"
                onclick="event.preventDefault();
                document.getElementById('supply-input').value='min';
                document.getElementById('supply-form').submit();"
        >
            Заказать минимальный
        </button>
        <form id="supply-form" method="post" action="{{ route('admin.accounting.distributor.supply', $distributor) }}">
            <input id="supply-input" type="hidden" name="balance" value="all">
            @csrf
        </form>

        <div class="ml-2">
            <a href="{{ route('admin.accounting.distributor.show', $distributor) }}" class="text-blue-800">Все</a> ({{ $count['all'] }}) |
            <a href="{{ route('admin.accounting.distributor.show', [$distributor, 'balance' => 'min']) }}" class="text-blue-800">Минимальный</a> ({{ $count['min'] }}) |
            <a href="{{ route('admin.accounting.distributor.show', [$distributor, 'balance' => 'empty']) }}" class="text-blue-800">Отсутствующий</a> ({{ $count['empty'] }}) |
            <a href="{{ route('admin.accounting.distributor.show', [$distributor, 'balance' => 'no_buy']) }}" class="text-blue-800">Не заказывать</a> ({{ $count['no_buy'] }})
        </div>
    </div>
    <div class="grid grid-cols-12 gap-4 mt-5">
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЗАКУПОЧНАЯ ЦЕНА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">НАЛИЧИЕ (-РЕЗЕРВ)</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">БАЛАНС</x-base.table.th>
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
