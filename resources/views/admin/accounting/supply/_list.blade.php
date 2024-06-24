<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.supply.show', $supply) }}"
           class="font-medium whitespace-nowrap">{{ $supply->htmlNum() . ' от ' . $supply->htmlDate() }}</a>
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $supply->distributor->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $supply->statusHTML() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $supply->getQuantity() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $supply->getComment() }}</x-base.table.td>

    <x-base.table.td class="w-32">
        <div class="flex justify-end items-center">
            @if($supply->isCreated())
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.supply.destroy', $supply) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
