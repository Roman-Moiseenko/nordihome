@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Отзывы на товары
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <form method="get" action="{{ route('admin.feedback.review.index') }}" class="flex w-full">
                <div>
                    <input type="radio" class="btn-check" name="filter" id="option1" autocomplete="off"
                           value="moderated" onclick="this.form.submit();" @if($filter == 'moderated') checked @endif>
                    <label class="btn btn-success" for="option1">На модерации
                        @if($count_moderated != 0)<span>{{ $count_moderated }}</span> @endif
                    </label>
                    <input type="radio" class="btn-check" name="filter" id="option2" autocomplete="off"
                           value="published" onclick="this.form.submit();" @if($filter == 'published') checked @endif>
                    <label class="btn btn-success" for="option2">Опубликованные</label>

                    <input type="radio" class="btn-check" name="filter" id="option3" autocomplete="off"
                           value="blocked" onclick="this.form.submit();" @if($filter == 'blocked') checked @endif>
                    <label class="btn btn-success" for="option3">Заблокированные</label>

                    <input type="radio" class="btn-check" name="filter" id="option4" autocomplete="off"
                           value="draft" onclick="this.form.submit();" @if($filter == 'draft') checked @endif>
                    <label class="btn btn-secondary" for="option4">Черновики</label>

                    <input type="radio" class="btn-check" name="filter" id="option5" autocomplete="off"
                           value="all" onclick="this.form.submit();" @if($filter == 'all') checked @endif>
                    <label class="btn btn-primary" for="option5">Все</label>

                </div>

            </form>
        </div>
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            {{ $reviews->links('admin.components.count-paginator') }}
        </div>
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap">ТОВАР</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">КЛИЕНТ</x-base.table.th>
                        <x-base.table.th class="w-20 whitespace-nowrap text-center">РЕЙТИНГ</x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap text-center">ДАТА</x-base.table.th>
                        <x-base.table.th class="w-32 whitespace-nowrap text-center">СТАТУС</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ОТЗЫВ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">PHOTO</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap text-center">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($reviews as $review)
                        @include('admin.feedback.review._list', ['review' => $review])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $reviews->links('admin.components.paginator', ['pagination' => $pagination]) }}



@endsection
