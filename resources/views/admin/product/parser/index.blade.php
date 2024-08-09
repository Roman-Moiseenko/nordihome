@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Спарсенные Товары
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            {{ $parsers->links('admin.components.count-paginator') }}
            <!-- Фильтр -->
                <div class="ml-auto">
                    <x-base.popover class="inline-block mt-auto" placement="left-start">
                        <x-base.popover.button as="x-base.button" variant="primary" class="button_counter"><i data-lucide="filter" width="20" height="20"></i>
                            @if($filters['count'] > 0)
                                <span>{{ $filters['count'] }}</span>
                            @endif
                        </x-base.popover.button>
                        <x-base.popover.panel>
                            <x-base.button id="close-add-group" class="ml-auto"
                                           data-tw-dismiss="dropdown" variant="secondary" type="button">
                                X
                            </x-base.button>
                            <form action="" METHOD="GET">
                                <div class="p-2">
                                    <input class="form-control" name="product" placeholder="Название, Артикул, Серия" value="{{ $filters['product'] }}" autocomplete="off">

                                    <x-base.tom-select id="select-category" name="category_id"
                                                       class="w-full mt-3" data-placeholder="Выберите категорию">
                                        <option value="" disabled selected>Выберите категорию</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $category->id == $filters['category'] ? 'selected' : ''}} >
                                                @for($i = 0; $i<$category->depth; $i++) - @endfor
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </x-base.tom-select>

                                    <div class="mt-3">
                                        <div class="form-check mr-3">
                                            <input id="published-all" class="form-check-input check-published" type="radio" name="published" value="all" {{ $filters['published'] == 'all' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="published-all">Все</label>
                                        </div>
                                        <div class="form-check mr-3 mt-2 sm:mt-0">
                                            <input id="published-active" class="form-check-input check-published" type="radio" name="published" value="active" {{ $filters['published'] == 'active' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="published-active">Опубликованные</label>
                                        </div>
                                        <div class="form-check mr-3 mt-2 sm:mt-0">
                                            <input id="published-draft" class="form-check-input check-published" type="radio" name="published" value="draft" {{ $filters['published'] == 'draft' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="published-draft">Черновики</label>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="form-check mr-3 mt-2 sm:mt-0">
                                            <input id="not-sale" class="form-check-input check-published" type="checkbox" name="not_sale"
                                                {{ !is_null($filters['not_sale']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="not-sale">Снятые с продажи</label>
                                        </div>
                                    </div>
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
                </div>
        </div>


        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="w-10 whitespace-nowrap">ИКОНКА</x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="w-32 text-center whitespace-nowrap">КАТЕГОРИЯ</x-base.table.th>
                        <x-base.table.th class="w-25 text-center whitespace-nowrap">КОЛ-ВО ПАЧЕК</x-base.table.th>
                        <x-base.table.th class="w-25 text-center whitespace-nowrap">ЦЕНА (Zl)</x-base.table.th>
                        <x-base.table.th class="w-10 text-center whitespace-nowrap">ХРУП.</x-base.table.th>
                        <x-base.table.th class="w-10 text-center whitespace-nowrap">САНК.</x-base.table.th>
                        <x-base.table.th class="w-10 text-center whitespace-nowrap">ДОСТ.</x-base.table.th>
                        <x-base.table.th class="w-32 text-center whitespace-nowrap">СОСТАВНОЙ</x-base.table.th>=
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($parsers as $parser)
                        @include('admin.product.parser._list', ['parser' => $parser])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>

    {{ $parsers->links('admin.components.paginator', ['pagination' => $pagination]) }}

@endsection
