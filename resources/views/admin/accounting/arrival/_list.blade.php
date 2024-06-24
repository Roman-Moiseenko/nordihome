<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.arrival.show', $arrival) }}"
           class="font-medium whitespace-nowrap">{{ $arrival->number . ' от ' . $arrival->created_at->format('d-m-Y') }}</a> {{ ($arrival->completed) ? '' : '(Черновик)' }}
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->distributor->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->storage->name }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->getInfoData()['quantity'] }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->getInfoData()['cost_currency'] }} {{ $arrival->currency->sign }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $arrival->getComment() }}</x-base.table.td>
    <x-base.table.td class="w-32">
        <div class="flex justify-end items-center">
            @if(!$arrival->isCompleted())
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.arrival.destroy', $arrival) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
