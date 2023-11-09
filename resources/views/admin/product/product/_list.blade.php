<x-base.table.tr>
    <x-base.table.td class="w-20">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
        </div>
    </x-base.table.td>
    <x-base.table.td class="w-40"><a href="{{ route('admin.product.show', $product) }}"
                                      class="font-medium whitespace-nowrap">{{ $product->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $product->category->name }}</x-base.table.td>
    <x-base.table.td class="text-center"> - </x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.product.edit', $product) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.destroy', $product) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
