@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-5">
            <h1 class="text-lg font-medium mr-auto">
                {{ $equivalent->name }}
            </h1>
        </div>
    </div>
    <div class="box p-5 mt-5">
        <x-base.popover class="inline-block mt-auto" placement="bottom-start">
            <x-base.popover.button as="x-base.button" variant="primary">Переименовать
                <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
            </x-base.popover.button>

            <x-base.popover.panel>
                <form action="{{ route('admin.product.equivalent.rename', $equivalent) }}" METHOD="POST">
                    @csrf
                    <div class="p-2">

                        <x-base.form-input name="name" class="flex-1 mt-2" type="text" placeholder="Уникальное имя"
                                           value="{{ $equivalent->name }}"/>

                        <div class="flex items-center mt-3">
                            <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown"
                                           variant="secondary" type="button">
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
        <span class="ml-3">Категория: {{ $equivalent->getCrumbsCategory() }}</span>
    </div>
    <form method="POST" action="{{ route('admin.product.equivalent.add-product', $equivalent) }}">
        @csrf
        <div class="box p-5 mt-5 flex">
            <x-base.tom-select id="select-product" name="product_id" class="w-1/2 mx-3"
                               data-placeholder="Выберите товар">
                <option value="0"></option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </x-base.tom-select>
            <button class="btn btn-primary shadow-md mr-2"
                    type="submit">Добавить товар в группу
            </button>
        </div>
    </form>

    <div class="col-span-12 overflow-auto lg:overflow-visible">
        <table class="table table-report -mt-2">
            <thead>
            <tr>
                <th class="whitespace-nowrap">IMG</th>
                <th class="whitespace-nowrap">ТОВАР</th>
                <th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</th>
            </tr>
            </thead>
            <tbody>
            @foreach($equivalent->products as $product)
                @include('admin.product.equivalent._list-product', ['product' => $product])
            @endforeach
            </tbody>
        </table>
    </div>

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите удалить товар из группы?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
