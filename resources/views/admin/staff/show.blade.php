@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h1 class="text-lg font-medium mr-auto">
            {{ $staff->fullName->getFullName() }}
        </h1>
    </div>
    <div class="intro-y box px-5 pt-5 mt-5">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                    <img class="rounded-full" src="{{ $staff->getPhoto() }}">
                    <div class="absolute mb-1 mr-1 flex items-center justify-center bottom-0 right-0 bg-primary rounded-full p-2"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="camera" class="lucide lucide-camera w-4 h-4 text-white" data-lucide="camera"><path d="M14.5 4h-5L7 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2h-3l-2.5-3z"></path><circle cx="12" cy="13" r="3"></circle></svg> </div>
                </div>
                <div class="ml-5">
                    <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">{{ $staff->fullName->getFullName() }}</div>
                    <div class="text-slate-500">{{ $staff->post }}</div>
                </div>
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3">Contact Details</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <i data-lucide="user" width="24" height="24" class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ $staff->name }} </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i data-lucide="mail" width="24" height="24" class="lucide lucide-mail w-4 h-4 mr-2"></i>
                        {{ $staff->email }} </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i data-lucide="phone" width="24" height="24" class="lucide lucide-phone w-4 h-4 mr-2"></i>
                        {{ $staff->phone }} </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <i data-lucide="key-round" width="24" height="24" class="lucide lucide-phone w-4 h-4 mr-2"></i>
                        {{ \App\Entity\Admin::ROLES[$staff->role] }} </div>
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
                <a class="btn btn-primary py-1 px-2 mr-2 {{ $staff->isBlocked() ? 'disabled' : '' }}"
                   href="{{ $staff->isBlocked() ? '' : route('admin.staff.edit', $staff) }}">Редактировать
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ $staff->isBlocked() ? '' : 'javascript:;' }}"
                   data-staff="{{ route('admin.staff.password', $staff) }}" data-fullname="{{ $staff->fullName->getShortName() }}"
                   data-tw-toggle="modal" data-tw-target="#password-modal"
                   class="btn btn-outline-secondary py-1 px-2 {{ $staff->isBlocked() ? 'disabled' : '' }} password-modal">
                    <i data-lucide="key-round" width="24" height="24" class="lucide lucide-key-round w-4 h-4 mr-2"></i>
                    Сменить пароль </a>
            </li>
        </ul>
    </div>

    <div class="intro-y box px-5 py-5 mt-5">
        Данные связанные с работой по профилю
    </div>


    <!-- Modal -->
    <div id="password-modal" data-tw-backdrop="static" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="form-password-modal" method="POST" action="{{ route('admin.home') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Сменить пароль сотруднику <strong id="fio"></strong></h3>
                    </div>
                    <div class="modal-body p-10 text-center">
                        <p>Введите новый пароль</p>
                        <input id="field-pass" class="form-control" type="text" name="password"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">
                            Отмена
                        </button>
                        <button type="submit" class="btn btn-primary w-20">Сохранить</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        let elements = document.getElementsByClassName("password-modal");
        let _user = document.getElementById('fio');
        let _form = document.getElementById('form-password-modal');
        Array.from(elements).forEach(function (element) {
            element.addEventListener('click', function () {
                let _id = element.getAttribute('data-staff');
                let _fio = element.getAttribute('data-fullname');
                _form.setAttribute('action', _id);
                _user.innerHTML = _fio;
            });
        });

    </script>
@endsection
