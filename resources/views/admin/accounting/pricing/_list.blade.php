<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.pricing.show', $pricing) }}"
           class="font-medium whitespace-nowrap">{{ $pricing->number . ' от ' . $pricing->created_at->format('d-m-Y') }}</a>
    </x-base.table.td>
    <x-base.table.td class="text-center">{{ $pricing->getManager() }}</x-base.table.td>
    <x-base.table.td class="text-center">
        @if(!empty($pricing->arrival))
        <a href="{{ route('admin.accounting.arrival.show', $pricing->arrival) }}">{{ '№ ' . $pricing->arrival->htmlNum() . ' от ' . $pricing->arrival->htmlDate() }}</a>
        @else
            -
        @endif
    </x-base.table.td>

    <x-base.table.td class="text-center"><x-yesNo status="{{ $pricing->isCompleted() }}" lucide="" class="justify-center"/></x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            @if(!$pricing->isCompleted())
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.accounting.pricing.destroy', $pricing) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
