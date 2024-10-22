@extends('layouts.admin')

@section('head')
    <title>Login NORDI HOME - CRM</title>
@endsection
@section('body')
login
@endsection
@section('content')

    <div class="container sm:px-10">
        <div class="block xl:grid grid-cols-2 gap-4">
            <!-- BEGIN: Login Info -->
            <div class="image-block hidden xl:flex flex-col min-h-screen">
                <a href="" class=" flex items-center pt-5">
                    <img alt="Nordi Home" class="w-6" src="{{ Vite::asset('resources/images/logo.png') }}">
                    <span class="text-white text-lg ml-3"> NORDI HOME </span>
                </a>
                <div class="my-auto">
                    <img alt="WebSite 39 CRM-Shop-Online" class="w-1/2 -mt-16"
                         src="{{ Vite::asset('resources/images/illustration.svg') }}">
                    <div class="text-white font-medium text-4xl leading-tight mt-10">
                        Система управления бизнесом
                        <br>
                        NORDI HOME
                    </div>
                    <div class="mt-5 text-lg text-white text-opacity-70 dark:text-slate-400">Управляйте своей сетью магазинов в одном месте
                    </div>
                </div>
            </div>
            <!-- END: Login Info -->
            <!-- BEGIN: Login Form -->
            <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                <div
                    class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                    <h2 class="font-bold text-2xl xl:text-3xl text-center xl:text-left">
                        Вход в кабинет
                    </h2>
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="mt-2 text-slate-400 xl:hidden text-center">Система управления бизнесом NORDI HOME. Управляйте своей сетью магазинов в одном месте
                        </div>
                        <div class="mt-5">
                            <input type="text" name="name" class="login__input form-control py-3 px-4 block"
                                   placeholder="Логин" id="name" value="{{ old('name') }}" required autocomplete="off"
                                   autofocus>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            <input type="password" name="password"
                                   class="login__input form-control py-3 px-4 block mt-4"
                                   placeholder="Пароль" autocomplete="off">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="flex text-slate-600 dark:text-slate-500 text-xs sm:text-sm mt-4">
                            <div class="flex items-center mr-auto">
                                <input id="remember" name="remember" type="checkbox"
                                       class="form-check-input border mr-2" {{ old('remember') ? 'checked' : '' }} autocomplete="off">
                                <label class="cursor-pointer select-none" for="remember">Запомнить меня</label>
                            </div>

                        </div>
                        <div class="mt-5 xl:mt-5 text-center xl:text-left">
                            <button type="submit" class="btn btn-primary py-3 px-4 w-full xl:w-32 xl:mr-3 align-top">Вход</button>
                            <!-- button class="btn btn-outline-secondary py-3 px-4 w-full xl:w-32 mt-3 xl:mt-0 align-top">
                                Register
                            </button -->
                        </div>
                        <div class="mt-10 xl:mt-24 text-slate-600 dark:text-slate-500 text-center xl:text-left">
                            Если вы забыли пароль, обратитесь к Администратору <br>
                            для предоставления доступа.
                        </div>
                    </form>
                </div>
            </div>
            <!-- END: Login Form -->
        </div>
    </div>



@endsection


