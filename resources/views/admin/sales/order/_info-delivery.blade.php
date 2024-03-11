<div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Доставка</div>
<div class="flex flex-col justify-center items-center lg:items-start mt-4">
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="truck" class="w-4 h-4"/>&nbsp;{{ $order->delivery->typeHTML() }}
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="map-pin" class="w-4 h-4"/>&nbsp;{{ $order->delivery->address }}
    </div>

    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="anvil" class="w-4 h-4"/>&nbsp;{{ $order->weight() }} кг
    </div>

    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="box" class="w-4 h-4"/>&nbsp;{{ $order->volume() }} м3
    </div>
    <div
        class="truncate sm:whitespace-normal flex items-center border-b w-100 border-slate-200/60 pb-2 mb-2"
        style="width: 100%;"></div>
    @if($order->isPoint())
        <div class="truncate sm:whitespace-normal flex items-center">
            <x-base.lucide icon="warehouse"
                           class="w-4 h-4"/>&nbsp;{{ 'Выдача/сборка: ' . $order->delivery->point->name }}
        </div>
    @endif

    @if($order->delivery->cost != 0)
        <div class="truncate sm:whitespace-normal flex items-center">
            <x-base.lucide icon="badge-russian-ruble"
                           class="w-4 h-4"/>&nbsp;Доставка&nbsp;{{ price($order->delivery->cost) }}
        </div>
    @endif
    @if(!empty($order->movements))
        <div class="truncate sm:whitespace-normal flex items-center flex-col">
            <div class="truncate sm:whitespace-normal flex items-center">
                <x-base.lucide icon="truck" class="w-4 h-4"/>&nbsp;Перемещения со складов:
            </div>
            @foreach($order->movements as $i => $movement)
                <div>
                    {{ '#' . (int)($i + 1) . ' ' . $movement->storageOut->name . ($movement->isCompleted() ? ': Исполнено' : ': В ожидании') }}
                </div>
            @endforeach
        </div>
    @endif
</div>
