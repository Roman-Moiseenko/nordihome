@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
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
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <form method="get" action="{{ url()->current() }}">
                <input type="radio" class="btn-check" name="filter" id="option2" autocomplete="off"
                       value="new" onclick="this.form.submit();" @if($filter == 'new') checked @endif>
                <label class="btn btn-success" for="option2">Новые
                    @if($filter_count['new'] != 0)<span>{{ $filter_count['new'] }}</span> @endif
                </label>

                <input type="radio" class="btn-check" name="filter" id="option3" autocomplete="off"
                       value="assembly" onclick="this.form.submit();" @if($filter == 'assembly') checked @endif>
                <label class="btn btn-success" for="option3">На сборке
                    @if($filter_count['assembly'] != 0)<span>{{ $filter_count['assembly'] }}</span> @endif
                </label>

                @if($type != \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_STORAGE)
                    <input type="radio" class="btn-check" name="filter" id="option4" autocomplete="off"
                           value="delivery" onclick="this.form.submit();" @if($filter == 'delivery') checked @endif>
                    <label class="btn btn-success" for="option4">На доставке
                        @if($filter_count['delivery'] != 0)<span>{{ $filter_count['delivery'] }}</span> @endif
                    </label>
                @endif

                <input type="radio" class="btn-check" name="filter" id="option6" autocomplete="off"
                       value="completed" onclick="this.form.submit();" @if($filter == 'completed') checked @endif>
                <label class="btn btn-secondary" for="option6">Завершенные</label>

                <input type="radio" class="btn-check" name="filter" id="option1" autocomplete="off"
                       value="all" onclick="this.form.submit();" @if($filter == 'all') checked @endif>
                <label class="btn btn-primary" for="option1">Все</label>

            </form>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">РАСПОРЯЖЕНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">АДРЕС</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СТАТУС</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($expenses as $expense)
                        @include('admin.delivery._list', ['expense' => $expense])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $expenses->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
