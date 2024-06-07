@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Возвраты
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->

        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <x-base.popover class="inline-block mt-auto mt-2" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary" class="">Создать Возврат
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form action="{{ route('admin.sales.refund.create') }}" METHOD="GET">
                        <div class="p-2">
                            <x-base.form-input name="order_id" class="flex-1 mt-2" type="text" value=""
                                               placeholder="№ Заказа"/>

                            <div class="flex items-center mt-3">
                                <x-base.button id="close-add-group" class="w-32 ml-auto"
                                               data-tw-dismiss="dropdown" variant="secondary" type="button">
                                    Отмена
                                </x-base.button>
                                <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                    Возврат
                                </x-base.button>
                            </div>
                        </div>
                    </form>
                </x-base.popover.panel>
            </x-base.popover>
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
                        <x-base.table.th class="whitespace-nowrap text-center">МЕНЕДЖЕР</x-base.table.th>
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
