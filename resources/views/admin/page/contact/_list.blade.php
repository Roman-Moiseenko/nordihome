<x-base.table.tr>
    <x-base.table.td class="w-40"><a href="{{ route('admin.page.contact.edit', $contact) }}"
                        class="font-medium whitespace-nowrap">{{ $contact->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center"><i class="{{ $contact->icon }}" style="color: {{ $contact->color }}"></i></x-base.table.td>
    <x-base.table.td class="text-center">{{ $contact->url }}</x-base.table.td>

    <x-base.table.td class="text-center"><x-yesNo status="{{ $contact->published }}" lucide="" class="justify-center"/></x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <form action="{{ route('admin.page.contact.up', $contact) }}" method="POST">
                @csrf
                <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()"><x-base.lucide icon="arrow-up" class="w-4 h-4"/></a>
            </form>
            <form action="{{ route('admin.page.contact.down', $contact) }}" method="POST">
                @csrf
                <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()"><x-base.lucide icon="arrow-down" class="w-4 h-4"/></a>
            </form>
            <a class="flex items-center mr-3" href="{{ route('admin.page.contact.edit', $contact) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            @if(!$contact->isDraft())
                <form action="{{ route('admin.page.contact.draft', $contact) }}" method="POST">
                    @csrf
                <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()">
                    <x-base.lucide icon="file-x" class="w-4 h-4"/>
                    Draft </a>
                </form>
            @else
                <form action="{{ route('admin.page.contact.published', $contact) }}" method="POST">
                    @csrf
                <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()">
                    <x-base.lucide icon="file-check" class="w-4 h-4"/>
                    Published </a>
                </form>
            @endif
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.page.contact.destroy', $contact) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
        </x-base.table.td>
</x-base.table.tr>
