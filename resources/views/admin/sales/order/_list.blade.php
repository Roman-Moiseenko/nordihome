<x-base.table.tr>
    <x-base.table.td class="text-center"> * </x-base.table.td>
    <x-base.table.td class="w-40 font-medium"><a href="{{ route('admin.sales.order.show', $order) }}">{{ $order->htmlNum() }}</a></x-base.table.td>
    <x-base.table.td class="w-40 font-medium"><a href="{{ route('admin.sales.order.show', $order) }}">{{ $order->created_at->translatedFormat('d F Y') }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ price($order->amount) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ '*' }} шт.</x-base.table.td>
    <x-base.table.td class="text-center">{{ price($order->total) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $order->status->value() }}</x-base.table.td>
    <x-base.table.td class="text-center font-medium"><a href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->email }}</a></x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">

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
