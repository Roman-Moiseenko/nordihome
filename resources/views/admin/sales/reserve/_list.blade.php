<x-base.table.tr>
    <x-base.table.td class="w-52">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
        </div>

    </x-base.table.td>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.edit', $product) }}"
                                     class="font-medium whitespace-nowrap">{{ $product->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $product->reserves->sum('quantity') }}</x-base.table.td>



</x-base.table.tr>
