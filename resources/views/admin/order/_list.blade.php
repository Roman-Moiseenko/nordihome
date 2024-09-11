<x-base.table.tr>
    <x-base.table.td class="text-center">{!! \App\Modules\Order\Helpers\OrderHelper::pictogram($order) !!}</x-base.table.td>
    <x-base.table.td class=""><a href="{{ route('admin.order.show', $order) }}" class="font-medium text-success">{{ $order->htmlNum() }}</a></x-base.table.td>
    <x-base.table.td class=""><a href="{{ route('admin.order.show', $order) }}" class="font-medium text-success">{{ $order->htmlShortDate() }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->getNameManager(true) }}</x-base.table.td>
    <x-base.table.td class="text-center">
        @if(!is_null($order->user_id))
            <a href="{{ route('admin.user.show', $order->user) }}">
                {{ $order->user->getPublicName() }}<br>
                {{ phone($order->user->phone) }}
            </a>
        @else
            Гость
        @endif
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->getType() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ price($order->getTotalAmount()) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->status->value() }}</x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.order.log', $order) }}">
                <x-base.lucide icon="history" class="w-4 h-4"/>
                Log </a>
            <a class="flex items-center mr-3" href="{{ route('admin.order.show', $order) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>

            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.order.destroy', $order) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
