@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $discount->name }}
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
                                       icon="calendar-clock"/>&nbsp;{{ $discount->caption() }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="percent"/>&nbsp;{{ $discount->discount }}
                    </div>

                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide class="w-4 h-4" icon="git-fork"/>&nbsp;{{ $discount->nameType() }}
                    </div>

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
            @if($discount->active())
                <li class="nav-item">
                    <form action="{{ route('admin.discount.discount.draft', $discount) }}" method="POST">
                        @csrf
                        <a class="btn btn-danger py-1 px-2 mr-2" href="#"
                           onclick="this.parentNode.submit()">Остановить</a>
                    </form>
                </li>
            @endif
            @if(!$discount->active())
                <li class="nav-item">
                    <form action="{{ route('admin.discount.discount.published', $discount) }}" method="POST">
                        @csrf
                        <a class="btn btn-success py-1 px-2 mr-2" href="#" onclick="this.parentNode.submit()">Запустить</a>
                    </form>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary py-1 px-2 mr-2"
                       href="{{ route('admin.discount.discount.edit', $discount) }}">Редактировать
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="btn btn-outline-secondary py-1 px-2"
                       data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal"
                       data-route= {{ route('admin.discount.discount.destroy', $discount) }}>
                        <i data-lucide="trash-2" width="24" height="24"
                           class="lucide lucide-key-round w-4 h-4 mr-2"></i>
                        Удалить </a>
                </li>
            @endif
        </ul>
    </div>


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
        'Вы действительно хотите удалить Скидку?<br>Этот процесс не может быть отменен.')->show() }}

@endsection
