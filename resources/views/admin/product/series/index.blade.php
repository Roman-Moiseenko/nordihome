@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Серии товаров
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <x-base.popover class="inline-block mt-auto" placement="bottom-start">
                <x-base.popover.button as="x-base.button" variant="primary" class=""
                                       id="button-supply-stack" type="button">
                    Добавить серию
                    <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                </x-base.popover.button>
                <x-base.popover.panel>
                    <form method="post" action="{{ route('admin.product.series.store') }}">
                        @csrf
                        <div class="p-2">
                            <input name="name" type="text" class="form-control" value="" placeholder="Название серии">

                            <div class="flex items-center mt-3">
                                <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown" variant="secondary" type="button">
                                    Отмена
                                </x-base.button>
                                <button class="w-32 ml-2 btn btn-primary" type="submit">
                                    Создать
                                </button>
                            </div>
                        </div>
                    </form>
                </x-base.popover.panel>
            </x-base.popover>

            {{ $list->links('admin.components.count-paginator') }}
        </div>

        <div class="col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
                <thead>
                <tr>
                    <th class="w-56 whitespace-nowrap">НАЗВАНИЕ</th>
                    <th class="w-40 text-center whitespace-nowrap">КОЛ-ВО ТОВАРОВ</th>
                    <th> </th>
                    <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $series)
                    <tr class="">
                        <td class=""><a href="{{ route('admin.product.series.show', $series) }}"
                                            class="font-medium whitespace-nowrap">{{ $series->name }}</a></td>
                        <td class="w-40 text-center whitespace-nowrap">{{ $series->products()->count() }}</td>
                        <td> </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center text-danger" href="#"
                                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.series.destroy', $series) }}
                                ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                                    Delete </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{ $list->links('admin.components.paginator', ['pagination' => $pagination]) }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить серию?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
