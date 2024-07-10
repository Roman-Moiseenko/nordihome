@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Группы атрибутов
        </h2>
    </div>
    <x-base.popover class="inline-block mt-auto mt-5 mb-2" placement="bottom-start">
        <x-base.popover.button as="x-base.button" variant="primary">Добавить<x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/></x-base.popover.button>

        <x-base.popover.panel>
            <form action="{{ route('admin.product.attribute.group-add') }}" METHOD="POST">
                @csrf
                <div class="p-2">
                    <div>
                        <div class="text-xs text-left">Группа</div>
                        <x-base.form-input name="name" class="flex-1 mt-2" type="text" placeholder="Уникальное имя"/>
                    </div>
                    <div class="flex items-center mt-3">
                        <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown" variant="secondary" type="button">
                            Отмена
                        </x-base.button>
                        <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                            Добавить
                        </x-base.button>
                    </div>
                </div>
            </form>
        </x-base.popover.panel>

    </x-base.popover>
    <div class="col-span-12 overflow-auto lg:overflow-visible">
        <x-base.table class="-mt-2 border-separate border-spacing-y-[10px]">
            <x-base.table.thead>
                <x-base.table.tr>
                    <x-base.table.th class="whitespace-nowrap border-b-0 w-1/3">
                        НАИМЕНОВАНИЕ
                    </x-base.table.th>
                    <x-base.table.th class="whitespace-nowrap border-b-0">

                    </x-base.table.th>
                    <x-base.table.th class="whitespace-nowrap border-b-0 text-center">
                        АТРИБУТЫ
                    </x-base.table.th>
                    <x-base.table.th class="whitespace-nowrap border-b-0 text-center">
                        ДЕЙСТВИЯ
                    </x-base.table.th>
                </x-base.table.tr>
            </x-base.table.thead>
            <x-base.table.tbody>

                @foreach ($groups as $group)
                    <x-base.table.tr class="">
                        <x-base.table.td
                            class="w-40 border-b-0 bg-white shadow-[20px_3px_20px_#0000000b] first:rounded-l-md last:rounded-r-md dark:bg-darkmode-600"
                        > {{ $group->name }}
                        </x-base.table.td>
                        <x-base.table.td
                            class="border-b-0 bg-white shadow-[20px_3px_20px_#0000000b] first:rounded-l-md last:rounded-r-md dark:bg-darkmode-600"
                        >
                            <x-base.popover class="inline-block mt-auto" placement="bottom-start">
                                <x-base.popover.button as="x-base.button" variant="primary">Переименовать<x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/></x-base.popover.button>

                                <x-base.popover.panel>
                                    <form action="{{ route('admin.product.attribute.group-rename', $group) }}" METHOD="POST">
                                        @csrf
                                        <div class="p-2">

                                            <x-base.form-input name="name" class="flex-1 mt-2" type="text" placeholder="Уникальное имя" value="{{ $group->name }}"/>

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
                        <x-base.table.td
                            class="border-b-0 bg-white text-center shadow-[20px_3px_20px_#0000000b] first:rounded-l-md last:rounded-r-md dark:bg-darkmode-600"
                        >
                            {{ count($group->attributes) }}
                        </x-base.table.td>
                        <x-base.table.td
                            class="table-report__action border-b-0 bg-white text-center shadow-[20px_3px_20px_#0000000b] first:rounded-l-md last:rounded-r-md dark:bg-darkmode-600"
                        >
                            <div class="flex justify-center items-center">

                                <a class="flex items-center mr-1" href="{{ route('admin.product.attribute.group-up', $group) }}" title="up"
                                   onclick="event.preventDefault(); document.getElementById('attribute-group-up-{{ $group->id }}').submit();">
                                    <x-base.lucide icon="arrow-up" class="w-4 h-4"/>
                                </a>
                                <form id="attribute-group-up-{{ $group->id }}" action="{{ route('admin.product.attribute.group-up', $group) }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                                <a class="flex items-center mr-4" href="{{ route('admin.product.attribute.group-down', $group) }}" title="down"
                                   onclick="event.preventDefault(); document.getElementById('attribute-group-down-{{ $group->id }}').submit();">
                                    <x-base.lucide icon="arrow-down" class="w-4 h-4"/>
                                </a>
                                <form id="attribute-group-down-{{ $group->id }}" action="{{ route('admin.product.attribute.group-down', $group) }}" method="POST" class="hidden">
                                    @csrf
                                </form>

                                <a class="flex items-center text-danger ml-6" href="#" title="Delete"
                                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.attribute.group-destroy', $group) }}>
                                    <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                                    Delete </a>
                            </div>
                        </x-base.table.td>

                    </x-base.table.tr>
                @endforeach
            </x-base.table.tbody>
        </x-base.table>
    </div>
    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите удалить Группу?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
