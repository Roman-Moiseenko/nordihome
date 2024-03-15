<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.departure.show', $departure) }}"
           class="font-medium whitespace-nowrap">{{ $departure->number . ' от ' . $departure->created_at->format('d-m-Y') }}</a> {{ ($departure->completed) ? '' : '(Черновик)' }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $departure->storage->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $departure->getInfoData()['quantity'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ price($departure->getInfoData()['cost']) }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if(!$departure->isCompleted())
            <a class="flex items-center mr-3" href="{{ route('admin.accounting.departure.edit', $departure) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.destroy', $departure) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
