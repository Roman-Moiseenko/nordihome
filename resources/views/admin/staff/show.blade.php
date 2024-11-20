@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h1 class="text-lg font-medium mr-auto">
            {{ $staff->fullname->getFullName() }}
        </h1>
    </div>
    <div class="box px-5 pt-5 mt-5">
        <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 -mx-5">
            <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                    <img class="rounded-full" src="{{ $staff->photo->getUploadUrl() }}">
                    <div class="absolute mb-1 mr-1 flex items-center justify-center bottom-0 right-0 bg-primary rounded-full p-2"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="camera" class="lucide lucide-camera w-4 h-4 text-white" data-lucide="camera"><path d="M14.5 4h-5L7 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2h-3l-2.5-3z"></path><circle cx="12" cy="13" r="3"></circle></svg> </div>
                </div>
                <div class="ml-5">
                    <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">{{ $staff->fullname->getFullName() }}</div>
                    <div class="text-slate-500">{{ $staff->post }}</div>
                </div>
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                <div class="font-medium text-center lg:text-left lg:mt-3">Контакты</div>
                <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                    <div class="truncate sm:whitespace-normal flex items-center">
                        <x-base.lucide icon="user" class="w-4 h-4"/>&nbsp;{{ $staff->name }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="mail" class="w-4 h-4"/>&nbsp;{{ $staff->email }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ phone($staff->phone) }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="send" class="w-4 h-4"/>&nbsp;{{ $staff->telegram_user_id }}
                    </div>
                    <div class="truncate sm:whitespace-normal flex items-center mt-3">
                        <x-base.lucide icon="key-round" class="w-4 h-4"/>&nbsp;{{ $staff->roleHTML() }}
                    </div>
                </div>
            </div>
            <div class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                @if($staff->isStaff())
                    <div class="font-medium text-center lg:text-left lg:mt-5">Доступы</div>
                    @foreach(\App\Modules\Admin\Entity\Responsibility::RESPONSE as $code => $name)
                        <div class="form-check mt-2">
                        <input id="response-{{ $code }}" type="checkbox" data-response="{{ $code }}"
                               class="form-check-input responsibility" {{ ($staff->isResponsibility($code)) ? 'checked' : '' }}>
                            <label for="response-{{ $code }}" class="form-check-label">{{ $name }}</label>
                        </div>
                    @endforeach
                @endif
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
                   data-staff="{{ route('admin.staff.password', $staff) }}" data-fullname="{{ $staff->fullname->getShortName() }}"
                   data-tw-toggle="modal" data-tw-target="#modal-change-password"
                   class="btn btn-outline-secondary py-1 px-2 {{ $staff->isBlocked() ? 'disabled' : '' }} password-modal">
                    <i data-lucide="key-round" width="24" height="24" class="lucide lucide-key-round w-4 h-4 mr-2"></i>
                    Сменить пароль </a>
            </li>
        </ul>
    </div>

    <div class="box px-5 py-5 mt-5">

        Данные связанные с работой по профилю
    </div>

    {{ \App\Forms\ModalPassword::create()->show() }}

    <script>
        let inputResponse = document.querySelectorAll('.responsibility');
        let route = "{{ route('admin.staff.response', $staff) }}";
        inputResponse.forEach(function (element) {
            element.addEventListener('click', function () {
                let data = element.getAttribute('data-response');
                setAjax(data, route);
            });
        })

        function setAjax(data, route) {
            let _params = '_token=' + '{{ csrf_token() }}' + '&code=' + data;
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let _data = JSON.parse(request.responseText);

                }
            };
        }
    </script>

@endsection
