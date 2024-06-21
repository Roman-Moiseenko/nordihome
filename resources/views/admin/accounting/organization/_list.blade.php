<x-base.table.tr>
    <x-base.table.td class="">
        <a href="{{ route('admin.accounting.organization.edit', $organization) }}"
           class="font-medium whitespace-nowrap">{{ $organization->short_name }}</a>
    </x-base.table.td>

    <x-base.table.td class="text-center">{{ $organization->INN }}</x-base.table.td>

    <x-base.table.td class="text-center">{{ $organization->chief->getShortName() }}</x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.accounting.organization.show', $organization) }}">
                <x-base.lucide icon="eye" class="w-4 h-4"/>
                View </a>
            <a class="flex items-center mr-3" href="{{ route('admin.accounting.organization.edit', $organization) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            @if(!$organization->isDefault())
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route={{ route('admin.accounting.organization.destroy', $organization) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
                @endif
        </div>
    </x-base.table.td>
</x-base.table.tr>
