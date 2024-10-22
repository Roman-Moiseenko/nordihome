@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h1 class="text-lg font-medium mr-auto">
            {{ $worker->fullname->getFullName() }}
        </h1>
    </div>
    <div class="box px-5 pt-5 mt-5">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3">Контакты</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="user" class="w-4 h-4"/>&nbsp;{{ $worker->fullname->getFullName() }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ phone($worker->phone) }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="send" class="w-4 h-4"/>&nbsp;{{ $worker->telegram_user_id }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="key-round" class="w-4 h-4"/>&nbsp;{{ $worker->postHTML() }}
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center py-5">
            <li class="nav-item">
                <a class="btn btn-primary py-1 px-2 mr-2 {{ $worker->isBlocked() ? 'disabled' : '' }}"
                   href="{{ $worker->isBlocked() ? '' : route('admin.worker.edit', $worker) }}">Редактировать
                </a>
            </li>

        </ul>
    </div>

    <div class="box px-5 py-5 mt-5">
        Данные связанные с работой по профилю<br><br>
        @if($worker->isDriver())
            Водитель - транспорт <br> Поездки
        @endif
        @if($worker->isLoader())
            Грузчик - склад <br> Собранные отгрузки, упаковки
        @endif
        @if($worker->isAssemble())
            Сборщик - транспорт <br> Поездки, собранные товары
        @endif

    </div>


@endsection
