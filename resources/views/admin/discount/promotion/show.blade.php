@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="flex items-center mt-5">
            <h1 class="text-lg font-medium mr-auto">
                {{ $promotion->name }}
            </h1>
        </div>
    </div>
    <div class="box px-5 pt-5 mt-5">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                    <img class="rounded-full" src="{{ $promotion->getImage() }}">
                    <div
                        class="absolute mb-1 mr-1 flex items-center justify-center bottom-0 right-0 bg-primary rounded-full p-2">
                        <x-base.lucide class="w-4 h-4 text-white" icon="camera"/>
                    </div>
                </div>
                <div class="ml-5">
                    <div class="flex">
                        <div class="truncate sm:whitespace-normal font-medium text-lg">{{ $promotion->name }}</div>
                        <div class="w-8 h-8 ml-3"><img class="rounded-full" src="{{ $promotion->getIcon() }}"></div>
                    </div>

                    <div class="text-slate-500 text-primary">{{ $promotion->slug }}</div>
                    <div class="mt-3">{!! App\Helpers\PromotionHelper::html($promotion) !!}</div>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3"></div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide class="w-4 h-4"
                                       icon="calendar-clock"/>&nbsp;{{ is_null($promotion->start_at) ? 'Запуск вручную' : $promotion->start_at->translatedFormat('j F Y') }}
                        - {{ $promotion->finish_at->translatedFormat('j F Y') }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="align-justify"/>&nbsp;{{ $promotion->title }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="percent"/>&nbsp;{{ $promotion->discount }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="file-text"/>&nbsp;{{ $promotion->description }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="link"/>&nbsp;<a href="{{ $promotion->condition_url }}"
                                                                             target="_blank">{{ $promotion->condition_url }}</a>
                    </div>
                    <x-yesNo title="Показывать в меню" status="{{$promotion->menu}}" class="mt-3"/>
                    <x-yesNo title="Заголовок в карточке товара" status="{{$promotion->show_title}}" class="mt-3"/>
                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                Статистические данные о продажах<br>
                Сколько товаров было посмотрено, продано, в избранное<br>
                Сколько клиентов купило<br>
                График продаж по дням<br>
            </div>
        </div>

        <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center py-5">
            @if($promotion->status() == \App\Modules\Discount\Entity\Promotion::STATUS_WAITING)
                <li class="nav-item">
                    <form action="{{ route('admin.discount.promotion.draft', $promotion) }}" method="POST">
                        @csrf
                        <a class="btn btn-pending py-1 px-2 mr-2" href="#" onclick="this.parentNode.submit()">В
                            черновики</a>
                    </form>
                </li>
                @if(is_null($promotion->start_at))
                    <li class="nav-item">
                        <form action="{{ route('admin.discount.promotion.start', $promotion) }}" method="POST">
                            @csrf
                            <a class="btn btn-primary py-1 px-2 mr-2" href="#" onclick="this.parentNode.submit()">Запустить</a>
                        </form>
                    </li>
                @endif
            @endif
            @if($promotion->status() == \App\Modules\Discount\Entity\Promotion::STATUS_STARTED)
                <li class="nav-item">
                    <form action="{{ route('admin.discount.promotion.stop', $promotion) }}" method="POST">
                        @csrf
                        <a class="btn btn-danger py-1 px-2 mr-2" href="#"
                           onclick="this.parentNode.submit()">Остановить</a>
                    </form>
                </li>
            @endif
            @if($promotion->status() == \App\Modules\Discount\Entity\Promotion::STATUS_DRAFT)
                <li class="nav-item">
                    <form action="{{ route('admin.discount.promotion.published', $promotion) }}" method="POST">
                        @csrf
                        <a class="btn btn-success py-1 px-2 mr-2" href="#" onclick="this.parentNode.submit()">Опубликовать</a>
                    </form>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary py-1 px-2 mr-2"
                       href="{{ route('admin.discount.promotion.edit', $promotion) }}">Редактировать
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="btn btn-outline-secondary py-1 px-2"
                       data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal"
                       data-route= {{ route('admin.discount.promotion.destroy', $promotion) }}>
                        <i data-lucide="trash-2" width="24" height="24"
                           class="lucide lucide-key-round w-4 h-4 mr-2"></i>
                        Удалить </a>
                </li>
            @endif
        </ul>
    </div>

    <div class="box p-5 flex items-center">
        <x-searchAddProduct route-save="{{ route('admin.discount.promotion.add-product', $promotion) }}" width="100"
                            published="1" caption="Добавить товар в Акцию"/>
        <x-listCodeProducts route="{{ route('admin.discount.promotion.add-products', $promotion) }}" caption-button="Добавить товары в акцию" class="ml-3"/>
    </div>


    @if(!empty($promotion->products))
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="text-center whitespace-nowrap">IMG</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">АРТИКУЛ</x-base.table.th>
                        <x-base.table.th class="whitespace-nowrap">НАЗВАНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЦЕНООБРАЗОВАНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДЕЙСТВИЯ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    @foreach($promotion->products as $product)
                        @include('admin.discount.promotion._list_product', ['product' => $product])
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
        </div>

    @endif

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить Акцию?<br>Этот процесс не может быть отменен.')->show() }}

    {{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите исключить товаров?<br>Вы всегда можете ее повторно добавить', 'delete-confirmation-modal-group')->show() }}

    <script>
        let selectGroup = document.getElementById('select-group');
        let discount = document.getElementById('input-discount');
        /* let elements = document.querySelectorAll(".show-children");
         Array.from(elements).forEach(function (element) {
             element.addEventListener('click', function (e) {
                 e.preventDefault();
                 let _show = element.getAttribute('show');
                 let _for = element.getAttribute('target');
                 let dropTable = document.getElementById(_for);
                 let td_chevron = element.querySelector('div');
                 if (_show === 'hide') {
                     element.setAttribute('show', 'visible');
                     dropTable.classList.remove('hidden');
                     td_chevron.classList.add('transform');
                     td_chevron.classList.add('rotate-180');

                 } else {
                     element.setAttribute('show', 'hide');
                     dropTable.classList.add('hidden');
                     td_chevron.classList.remove('transform');
                     td_chevron.classList.remove('rotate-180');
                 }
             });
         });*/
    </script>
    <script>
        let promotionProducts = document.querySelectorAll('.promotion-product');

        let arrayListens = Array.prototype.slice.call(promotionProducts);
        arrayListens.forEach(function (element) {
            element.addEventListener('change', function (item) {
                let route = element.getAttribute('data-route');
                let value = element.value;
                element.disabled = true;

                let _params = '_token=' + '{{ csrf_token() }}' + '&price=' + value;
                let request = new XMLHttpRequest();
                request.open('POST', route);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(_params);
                request.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        element.disabled = false;
                    } else {
                        //console.log(request.responseText);
                    }
                };
            })
        });
    </script>
@endsection
