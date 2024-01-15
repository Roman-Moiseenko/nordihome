<x-base.table.tr>
    <x-base.table.td class="w-40"><a href="{{ route('admin.page.page.show', $page) }}"
                        class="font-medium whitespace-nowrap">{{ $page->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $page->title }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $page->template }}</x-base.table.td>
    <x-base.table.td class="text-center"><x-yesno status="{{ $page->menu }}" lucide="" class="justify-center"/></x-base.table.td>

    <x-base.table.td class="text-center"><x-yesno status="{{ $page->published }}" lucide="" class="justify-center"/></x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.page.page.edit', $page) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            @if($page->published)
                <form action="{{ route('admin.page.page.draft', $page) }}" method="POST">
                    @csrf
                <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()">
                    <x-base.lucide icon="file-x" class="w-4 h-4"/>
                    Draft </a>
                </form>
            @else
                <form action="{{ route('admin.page.page.published', $page) }}" method="POST">
                    @csrf
                <a class="flex items-center mr-3" href="#" onclick="this.parentNode.submit()">
                    <x-base.lucide icon="file-check" class="w-4 h-4"/>
                    Published </a>
                </form>
            @endif
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.page.page.destroy', $page) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
        </x-base.table.td>
</x-base.table.tr>
