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
            <form method="get" action="{{ route('admin.sales.order.index') }}">
                <input type="radio" class="btn-check" name="filter" id="option1" autocomplete="off"
                       value="all" onclick="this.form.submit();" @if($filter == 'all') checked @endif>
                <label class="btn btn-primary" for="option1">Все</label>
                <input type="radio" class="btn-check" name="filter" id="option2" autocomplete="off"
                       value="new" onclick="this.form.submit();" @if($filter == 'new') checked @endif>
                <label class="btn btn-success" for="option2">Новые
                    @if($filter_count['new'] != 0)<span>{{ $filter_count['new'] }}</span> @endif
                </label>
                <input type="radio" class="btn-check" name="filter" id="option3" autocomplete="off"
                       value="awaiting" onclick="this.form.submit();" @if($filter == 'awaiting') checked @endif>
                <label class="btn btn-success" for="option3">На оплате
                    @if($filter_count['awaiting'] != 0)<span>{{ $filter_count['awaiting'] }}</span> @endif
                </label>

                <input type="radio" class="btn-check" name="filter" id="option4" autocomplete="off"
                       value="at-work" onclick="this.form.submit();" @if($filter == 'at-work') checked @endif>
                <label class="btn btn-success" for="option4">В работе
                    @if($filter_count['at-work'] != 0)<span>{{ $filter_count['at-work'] }}</span> @endif
                </label>
                <input type="radio" class="btn-check" name="filter" id="option5" autocomplete="off"
                       value="canceled" onclick="this.form.submit();" @if($filter == 'canceled') checked @endif>
                <label class="btn btn-secondary" for="option5">Отмененные</label>
                <input type="radio" class="btn-check" name="filter" id="option6" autocomplete="off"
                       value="completed" onclick="this.form.submit();" @if($filter == 'completed') checked @endif>
                <label class="btn btn-secondary" for="option6">Завершенные</label>

            </form>
        </div>
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.sales.order.create') }}'">Создать заказ
            </button>
            {{ $orders->links('admin.components.count-paginator') }}
        </div>
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-20 whitespace-nowrap"></x-base.table.th>
                        <x-base.table.th class="w-40 whitespace-nowrap">№</x-base.table.th>
                        <x-base.table.th class="w-40 whitespace-nowrap text-center">ДАТА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СУММА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ТОВАРОВ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ИТОГО</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">СТАТУС</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КЛИЕНТ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($orders as $order)

                        @include('admin.sales.order._list', ['order' => $order])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $orders->links('admin.components.paginator', ['pagination' => $pagination]) }}


@endsection
