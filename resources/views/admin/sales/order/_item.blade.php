<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $item->product->getImage() }}" alt="{{ $item->product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.show', $item->product) }}"
                                     class="font-medium whitespace-nowrap">{{ $item->product->name }}</a></x-base.table.td>

    <x-base.table.td class="text-center"> {{ $item->quantity }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ price($item->base_cost) }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ ($item->base_cost == $item->sell_cost) ? '-' : price($item->sell_cost) }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ $item->discountName() }} </x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($order->isManager())
            <a class="flex items-center mr-3" href="">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.sales.order.del-item', $item) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
            @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
