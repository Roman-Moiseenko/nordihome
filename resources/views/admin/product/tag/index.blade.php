@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Метки
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <x-base.popover class="inline-block mt-auto" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary">Добавить Метку
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>

                <x-base.popover.panel>
                    <form action="{{ route('admin.product.tag.create') }}" METHOD="POST">
                        @csrf
                        <div class="p-2">
                            <div>
                                <div class="text-xs text-left">Группа</div>
                                <x-base.form-input name="name" class="flex-1 mt-2" type="text"
                                                   placeholder="Уникальное имя"/>
                            </div>
                            <div class="flex items-center mt-3">
                                <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown"
                                               variant="secondary" type="button">
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
            {{ $tags->links('admin.components.count-paginator') }}
        </div>

        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">МЕТКА</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap"></x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">SLUG</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ТОВАРЫ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($tags as $tag)
                        @include('admin.product.tag._list', ['tag' => $tag])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $tags->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить Метку?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
