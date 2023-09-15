@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создание нового сотрудника
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.staff.store') }}">
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
                        <div class="input-group">
                            <div id="input-group-user" class="input-group-text">
                                <i data-lucide="user" width="16" height="16"></i></div>
                            <input type="text" name="name" class="form-control" placeholder="Логин" aria-describedby="input-group-user"
                                   value="{{ old('name') }}">
                            @error('name')
                                <div class="pristine-error text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group mt-3">
                            <div id="input-group-email" class="input-group-text">@</div>
                            <input type="text" class="form-control" name="email" placeholder="Email" aria-label="Email"
                                   aria-describedby="input-group-email" value="{{ old('email') }}">
                            @error('email')
                            <div class="pristine-error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group mt-3">
                            <div id="input-group-phone" class="input-group-text">
                                <i data-lucide="phone" width="16" height="16"></i></div>
                            <input type="text" class="form-control" name="phone" placeholder="Телефон"
                                   aria-label="Phone" aria-describedby="input-group-phone" value="{{ old('phone') }}">
                            @error('phone')
                            <div class="pristine-error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="input-group mt-3">
                            <div id="input-group-password" class="input-group-text">
                                <i data-lucide="key-round" width="16" height="16" ></i>
                            </div>
                            <input type="text" class="form-control" name="password" placeholder="Пароль"
                                   aria-label="password" aria-describedby="input-group-password" value="{{ old('password') }}">
                            @error('password')
                            <div class="pristine-error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
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
                                <div>
                                    <input id="regular-surname" type="text" name="surname" class="form-control" placeholder="Фамилия" value="{{ old('surname') }}">
                                </div>
                                <div class="mt-3">
                                    <input id="regular-firstname" type="text" name="firstname" class="form-control" placeholder="Имя" value="{{ old('firstname') }}">
                                </div>
                                <div class="mt-3">
                                    <input id="regular-secondname" type="text" name="secondname" class="form-control" placeholder="Отчество" value="{{ old('secondname') }}">
                                </div>
                            </div>
                            <div id="single-file-upload" class="col-span-12 lg:col-span-4">
                                <div class="preview">
                                    <div data-single="true"  action="/file-upload" class="dropzone dz-clickable">
                                        <div class="fallback"><input name="photo" type="file"/></div>
                                        <div class="dz-message" data-dz-message>
                                            <div class="text-lg font-medium">Перетащите файл или кликнете для загрузки.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Доступ и должность -->
            <div class="intro-y col-span-12">
                <div class="intro-y box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Уровень доступа
                        </h2>
                    </div>
                    <div class="p-5 grid grid-cols-12 gap-4">
                        <div class="intro-y col-span-12 lg:col-span-4">
                            <div>
                                <input id="regular-post" type="text" name="post" class="form-control" placeholder="Должность"  value="{{ old('post') }}">
                            </div>
                        </div>
                        <div class="intro-y col-span-12 lg:col-span-4">
                            <select name="role" class="form-select sm:mr-2" aria-label="Доступ">
                                @foreach($roles as $key => $role)
                                <option value="{{ $key }}" {{ old('role' == $key) ? 'selected' : '' }}>{{ $role }}</option>
                                @endforeach
                            </select>
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
