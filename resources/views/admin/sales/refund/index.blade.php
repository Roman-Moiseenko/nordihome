@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Заказы
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->

        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            {{ $refunds->links('admin.components.count-paginator') }}
        </div>
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-32 whitespace-nowrap">ДАТА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ЗАКАЗ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СУММА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СТАТУС</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">МЕНЕДЖЕР</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($refunds as $refund)
                        @include('admin.sales.refund._list', ['refund' => $refund])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $refunds->links('admin.components.paginator', ['pagination' => $pagination]) }}
@endsection
