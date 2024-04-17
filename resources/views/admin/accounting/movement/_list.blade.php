<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.movement.show', $movement) }}"
           class="font-medium whitespace-nowrap">{{ $movement->number . ' от ' . $movement->created_at->format('d-m-Y') }}</a> {{ ($movement->isDraft()) ? '(Черновик)' : '' }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $movement->statusHTML() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $movement->storageOut->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $movement->storageIn->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $movement->getInfoData()['quantity'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ price($movement->getInfoData()['cost']) }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if($movement->isDraft())
                @if(empty($movement->order()))
                <a class="flex items-center mr-3" href="{{ route('admin.accounting.movement.edit', $movement) }}">
                    <x-base.lucide icon="check-square" class="w-4 h-4"/>
                    Edit </a>
                @endif
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.movement.destroy', $movement) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
