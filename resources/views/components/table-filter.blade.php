<div class="table-filter">

    <x-base.popover class="inline-block mt-auto" placement="left-start">
        <x-base.popover.button as="x-base.button" variant="primary" class="button_counter"><i data-lucide="filter" width="20" height="20"></i>
            @if(!is_null($count))
                <span>{{ $count }}</span>
            @endif
        </x-base.popover.button>
        <x-base.popover.panel>
            <x-base.button id="close-add-group" class="ml-auto" data-tw-dismiss="dropdown" variant="secondary" type="button">X</x-base.button>
            <form action="" METHOD="GET">
                <div class="p-2">
                    {{ $slot }}
                    <div class="flex items-center mt-3">
                        <x-base.button id="clear-filter" class="w-32 ml-auto"
                                       variant="secondary" type="button">
                            Сбросить
                        </x-base.button>
                        <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                            Фильтр
                        </x-base.button>
                    </div>
                </div>
            </form>
        </x-base.popover.panel>
    </x-base.popover>

    @once
        @push('scripts')
            @vite('resources/js/components/table-filter.js')
        @endpush
    @endonce
</div>
