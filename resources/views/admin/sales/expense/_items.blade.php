<x-base.table.tr>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.edit', $item->orderItem->product) }}"
                                     class="font-medium whitespace-nowrap flex">
            <div class="image-fit w-10 h-10">
                <img class="rounded-full" src="{{ $item->orderItem->product->getImage() }}" alt="{{ $item->orderItem->product->name }}">
            </div>
            <div class="items-center flex pl-3">{{ $item->orderItem->product->name }}</div>
        </a></x-base.table.td>

    <x-base.table.td class="text-center">{{ $item->quantity }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $expense->storage->getItem($item->orderItem->product)->cell ?? '-' }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item->comment }}</x-base.table.td>
</x-base.table.tr>
