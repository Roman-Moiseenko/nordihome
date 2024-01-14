<x-base.table.tr>
    <x-base.table.td class="w-40"><a href="{{ route('admin.page.widget.show', $widget) }}"
                        class="font-medium whitespace-nowrap">{{ $widget->name }}</a></x-base.table.td>
    <x-base.table.td class="text-center">{{ $widget->getObject() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $widget->getName() }}</x-base.table.td>
    <x-base.table.td class="text-center">{{ $widget->templateName() }}</x-base.table.td>

    <x-base.table.td class="text-center"><x-yesno status="{{ $widget->active }}" lucide="" class="justify-center"/></x-base.table.td>

    <x-base.table.td class="table-report__action w-56">
        <div class="flex justify-center items-center">
            <a class="flex items-center mr-3" href="{{ route('admin.page.widget.edit', $widget) }}">
                <x-base.lucide icon="check-square" class="w-4 h-4"/>
                Edit </a>
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.page.widget.destroy', $widget) }}
            ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
        </x-base.table.td>
</x-base.table.tr>
