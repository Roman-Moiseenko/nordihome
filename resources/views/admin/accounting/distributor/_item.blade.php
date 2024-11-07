<x-base.table.tr class="
 {{ !$item->product->balance->buy ? 'text-gray-400' : (($item->product->getQuantity() == 0) ? 'text-danger' : ($item->product->isBalance() ? 'text-warning' : '')) }}
">
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="" src="{{ $item->product->getImage() }}" alt="{{ $item->product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-32">{{ $item->product->code }}</x-base.table.td>
    <x-base.table.td class=""><a href="{{ route('admin.product.edit', $item->product) }}"
                                     class="font-medium">{{ $item->product->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ price($item->product->getPriceCost()) . ' / ' . price($item->product->getPriceCost(true)) }}</x-base.table.td>
    <!--$item->cost . ' ' . $item->distributor->currency->sign-->
    <x-base.table.td class="text-center">
        {{ $item->product->getQuantity() }}
        @if($item->product->getQuantityReserve() > 0)
        (-{{ $item->product->getQuantityReserve() }})
        @endif
    </x-base.table.td>
    <x-base.table.td class="text-center">
        <livewire:admin.accounting.edit-balance :product="$item->product" />
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ price($item->product->getLastPrice()) }}</x-base.table.td>
</x-base.table.tr>
