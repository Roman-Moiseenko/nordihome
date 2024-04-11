<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.supply.show', $supply) }}"
           class="font-medium whitespace-nowrap">{{ $supply->number . ' от ' . $supply->created_at->format('d-m-Y') }}</a>
        {{ $supply->statusHTML() }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $supply->distributor->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $supply->statusHTML() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $supply->getQuantity() }}</x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($supply->isCreated())
            <a class="flex items-center mr-3" href="{{ route('admin.accounting.supply.edit', $supply) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.supply.destroy', $supply) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
