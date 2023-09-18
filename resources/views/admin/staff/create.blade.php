@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создание нового сотрудника
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.staff.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-12 gap-6 mt-5">
            <!-- Основные данные -->
            <div class="intro-y col-span-12 lg:col-span-6">
                <div class="intro-y box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Данные авторизации
                        </h2>
                    </div>
                    <div class="p-5">
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Логин'])->group(['icon' => 'user', 'size' => 16])
                            ->validate($message ?? '')->show() }}
                        {{ \App\Forms\Input::create('email', ['placeholder' => 'Email', 'class' => 'mt-3'])->group('@')
                            ->validate($message ?? '')->show() }}
                        {{ \App\Forms\Input::create('phone', ['placeholder' => 'Телефон', 'class' => 'mt-3'])->group(['icon' => 'phone', 'size' => 16])
                            ->validate($message ?? '')->show() }}
                        {{ \App\Forms\Input::create('password', ['placeholder' => 'Пароль', 'class' => 'mt-3'])->group(['icon' => 'key-round', 'size' => 16])
                            ->validate($message ?? '')->show() }}
                    </div>
                </div>
            </div>
            <!-- Фото + Фио -->
            <div class="intro-y col-span-12 lg:col-span-6">
                <div class="intro-y box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Персональные данные
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-12 lg:col-span-8">
                        {{ \App\Forms\Input::create('surname', ['placeholder' => 'Фамилия'])->show() }}
                        {{ \App\Forms\Input::create('firstname', ['placeholder' => 'Имя', 'class' => 'mt-3'])->show() }}
                        {{ \App\Forms\Input::create('secondname', ['placeholder' => 'Отчество', 'class' => 'mt-3'])->show() }}
                    </div>
                    <div id="single-file-upload" class="col-span-12 lg:col-span-4">
                        {{ \App\Forms\Upload::create('file')->show() }}
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Доступ и должность -->
        <div class="intro-y col-span-12 mt-5">
            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">
                        Уровень доступа
                    </h2>
                </div>
                <div class="p-5 grid grid-cols-12 gap-4">
                    <div class="intro-y col-span-12 lg:col-span-4">
                        {{ \App\Forms\Input::create('post', ['placeholder' => 'Должность'])->show() }}
                    </div>
                    <div class="intro-y col-span-12 lg:col-span-4">
                        {{ \App\Forms\Select::create('role', ['placeholder' => 'Доступ'])->options($roles)->show() }}
                    </div>
                    <div class="intro-y col-span-12 lg:col-span-4 text-right">
                        <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection
