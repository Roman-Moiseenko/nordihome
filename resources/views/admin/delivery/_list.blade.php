<x-base.table.tr>
    <x-base.table.td class="w-52">

            <a href="{{ route('admin.sales.order.show', $delivery->order_id) }}"> {{ $delivery->order->htmlNum() }} </a>

    </x-base.table.td>
    <x-base.table.td class="w-40"> {{ $delivery->created_at->translatedFormat('d F Y') }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $delivery->address }}</x-base.table.td>
    <x-base.table.td class="text-center"> {{ $delivery->status->value() }} </x-base.table.td>
    @if($type != \App\Modules\Delivery\Entity\DeliveryOrder::STORAGE)
    <x-base.table.td class="text-center"> {{ price($delivery->cost) }} </x-base.table.td>
    @endif
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ "route('admin.delivery.cost', 'delivery')" }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ ' + '  }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
