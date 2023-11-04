<x-base.table.tr>
    <x-base.table.td class=""><a href="{{ route('admin.product.equivalent.show', $equivalent) }}"
                                      class="font-medium whitespace-nowrap">{{ $equivalent->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ count($equivalent->products) }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $equivalent->category->name }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.product.equivalent.edit', $equivalent) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.equivalent.destroy', $equivalent) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
