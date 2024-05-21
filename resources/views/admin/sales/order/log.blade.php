@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            История заказа {{ $order->htmlNumDate() }}
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЕ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОБЪЕКТ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ЗНАЧЕНИЕ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">МЕНЕДЖЕР</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДАТА</x-base.table.th>

                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($order->logs as $log)

                        <x-base.table.tr>
                            <x-base.table.td class="text-center">{{ $log->action }}</x-base.table.td>
                            <x-base.table.td class="text-center">{{ $log->object }}</x-base.table.td>
                            <x-base.table.td class="text-center">{{ $log->value }}</x-base.table.td>
                            <x-base.table.td class="text-center">{{ $log->staff->fullname->getFullname() }}</x-base.table.td>
                            <x-base.table.td class="text-center">{{ $log->htmlDate() }}</x-base.table.td>
                        </x-base.table.tr>

                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

@endsection
