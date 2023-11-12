@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Сотрудники компании
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.staff.create') }}'">Добавить сотрудника
            </button>

            {{ $admins->links('admin.components.count-paginator') }}
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <form id="search-role-form" action="?" method="GET">
                    {{ \App\Forms\Select::create('role', ['placeholder' => 'Доступ', 'value' => $selected])->options(array_merge(['' => ''], \App\Entity\Admin::ROLES))->show() }}
                    </form>
                </div>
            </div>
        </div>
    @foreach($admins as $staff)
        @include('admin.staff._card', ['staff' => $staff])
    @endforeach
    <!-- Modal -->

    </div>

    {{ $admins->links('admin.components.paginator', ['pagination' => $pagination, 'card' => true]) }}

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
        /** Модальное окно для изменения пароля */
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
        /** Фильтр по типу сотрудников */
        let selectRole = document.getElementById("select-role");
        let searchForm = document.getElementById("search-role-form");
        selectRole.addEventListener('change', () => {
            searchForm.submit();
        })
    </script>
@endsection

