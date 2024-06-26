<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $item->product->getImage() }}" alt="{{ $item->product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-32">{{ $item->product->code }}</x-base.table.td>

    <x-base.table.td class="w-40"><a href="{{ route('admin.product.edit', $item->product) }}"
                                      class="font-medium whitespace-nowrap">{{ $item->product->name }}</a> {{ ($item->product->published) ? '' : '(Черновик)' }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item->product->category->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item->quantity }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $item->inMovementHTML() }}</x-base.table.td>
    <x-base.table.td class="text-center"> {{ $item->getQuantityReserve() }} </x-base.table.td>
    <x-base.table.td class="text-center">{{ $item->product->getCountSell() }}</x-base.table.td>

</x-base.table.tr>
