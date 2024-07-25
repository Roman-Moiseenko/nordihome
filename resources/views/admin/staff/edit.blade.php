@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование профиля сотрудника
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-12 gap-6 mt-5">
            <!-- Основные данные -->
            <div class="col-span-12 lg:col-span-6">
                <div class="box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Данные авторизации
                        </h2>
                    </div>
                    <div class="p-5">
                        {{ \App\Forms\Input::create('name', ['placeholder' => 'Логин', 'value' => $staff->name])->group(['icon' => 'user', 'size' => 16])
                            ->validate($message ?? '')->show() }}
                        {{ \App\Forms\Input::create('email', ['placeholder' => 'Email', 'value' => $staff->email, 'class' => 'mt-3', 'class_input' => 'mask-email'])->group('@')
                            ->validate($message ?? '')->show() }}
                        {{ \App\Forms\Input::create('phone',
                            ['placeholder' => 'Телефон', 'value' => $staff->phone, 'class' => 'mt-3', 'class_input' => 'mask-phone'])
                            ->group(['icon' => 'phone', 'size' => 16])
                            ->validate($message ?? '')->show() }}
                        {{ \App\Forms\Input::create('chat_id', ['placeholder' => 'Чат ID телеграм', 'value' => $staff->telegram_user_id, 'class' => 'mt-3'])->group(['icon' => 'send', 'size' => 16])
                            ->validate($message ?? '')->show() }}
                    </div>
                </div>
            </div>
            <!-- Фото + Фио -->
            <div class="col-span-12 lg:col-span-6">
                <div class="box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Персональные данные
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-12 lg:col-span-8">
                                {{ \App\Forms\Input::create('surname', ['placeholder' => 'Фамилия', 'value' => $staff->fullname->surname])->show() }}
                                {{ \App\Forms\Input::create('firstname', ['placeholder' => 'Имя', 'value' => $staff->fullname->firstname, 'class' => 'mt-3'])->show() }}
                                {{ \App\Forms\Input::create('secondname', ['placeholder' => 'Отчество', 'value' => $staff->fullname->secondname, 'class' => 'mt-3'])->show() }}
                            </div>
                            <div id="single-file-upload" class="col-span-12 lg:col-span-4">
                                {{ \App\Forms\Upload::create('file', $staff->photo->getUploadUrl())->show() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Доступ и должность -->
            <div class="col-span-12">
                <div class="box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Уровень доступа
                        </h2>
                    </div>
                    <div class="p-5 grid grid-cols-12 gap-4">
                        <div class="col-span-12 lg:col-span-4">
                            {{ \App\Forms\Input::create('post', ['placeholder' => 'Должность', 'value' => $staff->post])->show() }}
                        </div>
                        <div class="col-span-12 lg:col-span-4">
                            {{ \App\Forms\Select::create('role', ['placeholder' => 'Доступ', 'value' => $staff->role ])->disabled($staff->isCurrent() )->options($roles)->show() }}

                        </div>
                        <div class="col-span-12 lg:col-span-4 text-right">
                            <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
