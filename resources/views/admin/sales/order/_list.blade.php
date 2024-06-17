<x-base.table.tr>
    <x-base.table.td class="text-center">{!! \App\Modules\Order\Helpers\OrderHelper::pictogram($order) !!}</x-base.table.td>
    <x-base.table.td class=""><a href="{{ route('admin.sales.order.show', $order) }}" class="font-medium text-success">{{ $order->htmlNum() }}</a></x-base.table.td>
    <x-base.table.td class=""><a href="{{ route('admin.sales.order.show', $order) }}" class="font-medium text-success">{{ $order->htmlShortDate() }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->getNameManager(true) }}</x-base.table.td>
    <x-base.table.td class="text-center"><a href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->email }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ price($order->getBaseAmount()) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->getQuantity() }} шт.</x-base.table.td>
    <x-base.table.td class="text-center">{{ price($order->getTotalAmount()) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->status->value() }}</x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.sales.order.log', $order) }}">
                <x-base.lucide icon="history" class="w-4 h-4"/>
                Log </a>
            <a class="flex items-center mr-3" href="{{ route('admin.sales.order.show', $order) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>

            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.sales.order.destroy', $order) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
