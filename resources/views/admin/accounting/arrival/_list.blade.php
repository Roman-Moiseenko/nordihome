<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.arrival.show', $arrival) }}"
           class="font-medium whitespace-nowrap">{{ $arrival->number . ' от ' . $arrival->created_at->format('d-m-Y') }}</a> {{ ($arrival->completed) ? '' : '(Черновик)' }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->distributor->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->storage->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->getInfoData()['quantity'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->getInfoData()['cost_currency'] }} {{ $arrival->currency->sign }}</x-base.table.td>
    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if(!$arrival->isCompleted())
            <a class="flex items-center mr-3" href="{{ route('admin.accounting.arrival.edit', $arrival) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.destroy', $arrival) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
