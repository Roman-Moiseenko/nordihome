@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $page->name }}
            </h1>
        </div>
    </div>
    <div class="intro-y box px-5 pt-5 mt-5">

        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3"></div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide class="w-4 h-4"
                                       icon="folder-open"/>&nbsp;{{ $page->name }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="package"/>&nbsp;{{ $page->title }}
                    </div>

                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="file-code-2"/>&nbsp;
                    </div>

                </div>
            </div>
            <div
                class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                Статистические данные о странице или визуализация страницы<br>
            </div>
        </div>

        <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center py-5">
            <li class="nav-item">
                <a class="btn btn-primary py-1 px-2 mr-2"
                   href="{{ route('admin.page.page.edit', $page) }}">Редактировать
                </a>
            </li>
            @if($page->published)
                <li class="nav-item">
                    <form action="{{ route('admin.page.page.draft', $page) }}" method="POST">
                        @csrf
                        <a class="btn btn-danger py-1 px-2 mr-2" href="#"
                           onclick="this.parentNode.submit()">В черновики</a>
                    </form>
                </li>
            @else
                <li class="nav-item">
                    <form action="{{ route('admin.page.page.published', $page) }}" method="POST">
                        @csrf
                        <a class="btn btn-success py-1 px-2 mr-2" href="#" onclick="this.parentNode.submit()">Опубликовать</a>
                    </form>
                </li>
            @endif
            <li class="nav-item">
                <a href="#" class="btn btn-outline-secondary py-1 px-2"
                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal"
                   data-route= {{ route('admin.page.page.destroy', $page) }}>
                    <i data-lucide="trash-2" width="24" height="24"
                       class="lucide lucide-key-round w-4 h-4 mr-2"></i>
                    Удалить </a>
            </li>
        </ul>
    </div>


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить Страницу?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
