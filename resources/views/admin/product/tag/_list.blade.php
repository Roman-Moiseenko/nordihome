<x-base.table.tr>
    <x-base.table.td class="">
        {{ $tag->name }}
    </x-base.table.td>
    <x-base.table.td class="">
        <x-base.popover class="inline-block mt-auto" placement="bottom-start">
            <x-base.popover.button as="x-base.button" variant="primary">Переименовать<x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/></x-base.popover.button>
            <x-base.popover.panel>
                <form action="{{ route('admin.product.tag.rename', $tag) }}" METHOD="POST">
                    @csrf
                    <div class="p-2">

                        <x-base.form-input name="name" class="flex-1 mt-2" type="text" placeholder="Уникальное имя" value="{{ $tag->name }}"/>

                        <div class="flex items-center mt-3">
                            <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown" variant="secondary" type="button">
                                Отмена
                            </x-base.button>
                            <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                Сохранить
                            </x-base.button>
                        </div>
                    </div>
                </form>
            </x-base.popover.panel>
        </x-base.popover>
    </x-base.table.td>
    <x-base.table.td class="">
        <span class="text-slate-500 flex items-center">
            <x-base.lucide icon="external-link" class="w-4 h-4"/> {{ $tag->getSlug() }}
        </span>
    </x-base.table.td>
    <x-base.table.td class="w-40 text-center">
        {{ count($tag->products) }}
    </x-base.table.td>

    <x-base.table.td class="table-report__action w-52">
        <div class="flex justify-center items-center">
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.tag.destroy', $tag) }}
               ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        </div>
    </x-base.table.td>
</x-base.table.tr>
