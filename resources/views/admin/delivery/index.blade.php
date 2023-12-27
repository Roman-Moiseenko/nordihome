@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            @if($type == \App\Modules\Delivery\Entity\DeliveryOrder::LOCAL)
                Заказы на доставку по области
            @endif
            @if($type == \App\Modules\Delivery\Entity\DeliveryOrder::REGION)
                Заказы на доставку транспортной компанией
            @endif
            @if($type == \App\Modules\Delivery\Entity\DeliveryOrder::STORAGE)
                Самовывоз
            @endif

        </h2>
    </div>
    <div class="intro-y box p-5 mt-5">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12 lg:col-span-3">
                <x-base.form-label for="select-status">Статус</x-base.form-label>
                <x-base.tom-select id="select-status" name="status"
                                   class="w-full" data-placeholder="Выберите статус">
                    <option value="0"></option>
                    @foreach(\App\Modules\Delivery\Entity\DeliveryStatus::STATUSES as $code => $caption)
                        <option value="{{ $code }}" {{ $code == $status ? 'selected' : ''}}>
                            {{ $caption }}
                        </option>
                    @endforeach
                </x-base.tom-select>
            </div>
        </div>
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
                        @if($type != \App\Modules\Delivery\Entity\DeliveryOrder::STORAGE)
                        <x-base.table.th class="text-center whitespace-nowrap">СТОИМОСТЬ</x-base.table.th>
                        @endif
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($deliveries as $delivery)
                        @include('admin.delivery.local._list', ['delivery' => $delivery])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $deliveries->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
