@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            @if($type == \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_LOCAL)
                Заказы на доставку по области
            @endif
            @if($type == \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_REGION)
                Заказы на доставку транспортной компанией
            @endif
            @if($type == \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_STORAGE)
                Самовывоз
            @endif

        </h2>
    </div>

    <script>
        /* Filters */
        const urlParams = new URLSearchParams(window.location.search);

        let selectStatus = document.getElementById('select-status');
        selectStatus.addEventListener('change', function () {
            let p = selectStatus.options[selectStatus.selectedIndex].value;
            urlParams.set('status', p);
            window.location.search = urlParams;
        });

        })
    </script>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ЗАКАЗ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ДАТА</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">АДРЕС</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СТАТУС</x-base.table.th>
                        @if($type != \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_STORAGE)
                        <x-base.table.th class="text-center whitespace-nowrap">СТОИМОСТЬ</x-base.table.th>
                        @endif
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($deliveries as $delivery)
                        @include('admin.delivery._list', ['delivery' => $delivery])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $deliveries->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
