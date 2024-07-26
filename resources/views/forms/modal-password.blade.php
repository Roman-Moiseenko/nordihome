<x-base.dialog id="{{ $id }}">
    <x-base.dialog.panel>
        <div class="modal-header">
            <h3>Сменить пароль сотруднику <strong id="fio"></strong></h3>
        </div>
        <form id="form-password-modal" method="POST" action="{{ route('admin.home') }}">
            @csrf
            <div class="modal-body p-10 text-center">
                <p>Введите новый пароль</p>
                <input id="field-pass" class="form-control" type="text" name="password" autocomplete="off"/>
            </div>
            <div class="modal-footer">
                <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">
                    Отмена
                </button>
                <button type="submit" class="btn btn-primary w-20">Сохранить</button>
            </div>
        </form>
    </x-base.dialog.panel>
</x-base.dialog>

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
</script>


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
                    <input id="field-pass" class="form-control" type="text" name="password" autocomplete="off"/>
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
