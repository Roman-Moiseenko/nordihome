@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Уведомления/Подписки
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-40 whitespace-nowrap text-center">НАЗВАНИЕ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ЗАГОЛОВОК</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОПИСАНИЕ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ЗАПУЩЕНО</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ПОДПИСЧИКОВ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КЛАСС</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($subscriptions as $subscription)
                        @include('admin.user.subscription._list', ['subscription' => $subscription])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>


@endsection
