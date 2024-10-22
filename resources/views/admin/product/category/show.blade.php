@extends('layouts.side-menu')

@section('subcontent')
<div>
    <div class="flex items-center mt-5">
        <h1 class="text-lg font-medium mr-auto">
            {{ $category->name }}
        </h1>
    </div>
</div>
<div class="box px-5 pt-5 mt-5">

    <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
        <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
            <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                <img class="rounded-full" src="{{ $category->getImage() }}">
                <div class="absolute mb-1 mr-1 flex items-center justify-center bottom-0 right-0 bg-primary rounded-full p-2">
                    <x-base.lucide class="w-4 h-4 text-white" icon="camera"/>
                </div>
            </div>
            <div class="ml-5">
                <div class="flex">
                    <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">{{ $category->name }}</div>
                    <div class="w-8 h-8"><img class="rounded-full" src="{{ $category->getIcon() }}"></div>
                </div>

                <div class="text-slate-500 text-primary">{{ $category->slug }}</div>
            </div>
        </div>
        <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
            <div class="font-medium text-center lg:text-left lg:mt-3">Meta-теги</div>
            <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                <div class="truncate sm:whitespace-normal flex items-center">
                    <i data-lucide="link" width="24" height="24" class="lucide lucide-mail w-4 h-4 mr-2"></i>
                    {{ $category->title }} </div>
                <div class="truncate sm:whitespace-normal flex items-center mt-3">
                    <i data-lucide="file-text" width="24" height="24" class="lucide lucide-mail w-6 h-6 mr-2"></i>
                    {{ $category->description }} </div>
            </div>
        </div>
        <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
            <div class="font-medium text-center lg:text-left lg:mt-5">Sales Growth</div>
            <div class="flex items-center justify-center lg:justify-start mt-2">
                <div class="mr-2 w-20 flex"> USP: <span class="ml-3 font-medium text-success">+23%</span> </div>
                <div class="w-3/4">
                    <div class="h-[55px]">
                        <canvas class="simple-line-chart-1 -mr-5" width="733" height="137" style="display: block; box-sizing: border-box; height: 54.8px; width: 293.2px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center lg:justify-start">
                <div class="mr-2 w-20 flex"> STP: <span class="ml-3 font-medium text-danger">-2%</span> </div>
                <div class="w-3/4">
                    <div class="h-[55px]">
                        <canvas class="simple-line-chart-2 -mr-5" width="733" height="137" style="display: block; box-sizing: border-box; height: 54.8px; width: 293.2px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center py-5">
        <li class="nav-item">
            <a class="btn btn-primary py-1 px-2 mr-2"
               href="{{ route('admin.product.category.edit', $category) }}">Редактировать
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="btn btn-outline-secondary py-1 px-2"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.category.destroy', $category) }}>
                <i data-lucide="trash-2" width="24" height="24" class="lucide lucide-key-round w-4 h-4 mr-2"></i>
                Удалить </a>
        </li>
    </ul>
</div>
<div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-4">
    <button class="btn btn-primary shadow-md mr-2"
            onclick="window.location.href='{{ route('admin.product.category.child', $category) }}'">Добавить Подкатегорию
    </button>
</div>
@if(count($category->children) > 0)
    <table class="table table-report">
        <tbody>
        @foreach($category->children()->defaultOrder()->get() as $children)
            @include('admin.product.category._list', ['category' => $children])
        @endforeach
        </tbody>
    </table>
@endif

{{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите удалить категорию?<br>Этот процесс не может быть отменен.')->show() }}

<script>
    let elements = document.querySelectorAll(".show-children");
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
    });
</script>
@endsection
