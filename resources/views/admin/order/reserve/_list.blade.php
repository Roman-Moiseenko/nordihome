<x-base.table.tr>
    <x-base.table.td class="w-10">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-32 text-center">{{ $product->code }}</x-base.table.td>
    <x-base.table.td class="w-52 text-center"><a href="{{ route('admin.product.show', $product) }}"
                                     class="font-medium whitespace-nowrap">{{ $product->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $product->getReserveCount() }}</x-base.table.td>
</x-base.table.tr>
