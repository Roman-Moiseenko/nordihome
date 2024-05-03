@extends('cabinet.cabinet')

@section('title', 'Мой кабинет - NORDI HOME')

@section('h1', 'Мой кабинет')

@section('subcontent')
    <h3 class="mt-1">Персональные данные</h3>
    <div class="box-card view-option">
        <div class="field mt-3">
            <span class="label">Фамилия Имя Отчество:</span>
            <span id="data-fullname" class="data"
                  style="display: {{ $user->fullname->isEmpty() ? 'none' :' inherit' }};">{{ $user->fullname->getFullName() }}</span>
            <div id="group-fullname" class="input-group" style="display: {{ !$user->fullname->isEmpty() ? 'none' :' inherit' }};">
                <input type="text" class="form-control" id="input-fullname" aria-describedby="Фамилия получателя" placeholder="Фамилия Имя Отчество"  autocomplete="off" readonly onfocus="this.removeAttribute('readonly');" onblur="this.setAttribute('readonly','');">
                <button id="save-fullname" class="btn btn-outline-secondary" type="button"
                        data-route="{{ route('cabinet.fullname', $user) }}"
                >Сохранить</button>
            </div>
            <button id="change-fullname" class="change btn btn-outline-primary"
                    style="display: {{ $user->fullname->isEmpty() ? 'none' :' inherit' }};">Изменить</button>
        </div>
        <div class="field mt-3">
            <span class="label">Контактный телефон:</span>
            <span id="data-phone" class="data"
                  style="display: {{ empty($user->phone) ? 'none' :' inherit' }};" >{{ $user->phone }}</span>
            <div id="group-phone" class="input-group" style="display: {{ !empty($user->phone) ? 'none' :' inherit' }};">
                <input type="text" class="form-control" id="input-phone" aria-describedby="Телефон получателя" placeholder="Телефон">
                <button id="save-phone" class="btn btn-outline-secondary" type="button"
                        data-route="{{ route('cabinet.phone', $user) }}"
                >Сохранить</button>
            </div>
            <button id="change-phone" class="change btn btn-outline-primary"
                    style="display: {{ empty($user->phone) ? 'none' :' inherit' }};">Изменить</button>
        </div>
    </div>

    <h3 class="mt-1">Данные для входа</h3>
    <div class="box-card view-option">
        <div class="field mt-3">
            <span class="label">Email для входа:</span>
            <span id="data-email" class="data" style="display: {{ empty($user->email) ? 'none' :' inherit' }};" >{{ $user->email }}</span>
            <div id="group-email" class="input-group" style="display: {{ !empty($user->email) ? 'none' :' inherit' }};">
                <input type="text" class="form-control" id="input-email" aria-describedby="Email получателя" placeholder="Email">
                <button id="save-email" class="btn btn-outline-secondary" type="button"
                        data-route="{{ route('cabinet.email', $user) }}"
                >Сохранить</button>
            </div>
            <button id="change-email" class="change btn btn-outline-primary" style="display: {{ empty($user->email) ? 'none' :' inherit' }};">Изменить</button>
        </div>
        <div class="inform mt-1 fs-87 text-danger">* После смены электронной почты кабинет будет недоступен, пока вы не подтвердите новую почту</div>
        <div class="password mt-3">
            <button id="change-password" class="btn btn-outline-primary">Сменить пароль</button>
            <div id="group-password" class="input-group" style="display: none">
                <input type="password" class="form-control" name="password" id="input-password" placeholder="Пароль"
                       minlength="6" required autocomplete="on" aria-describedby="show-hide-password">
                <button id="show-hide-password" class="btn btn-secondary" type="button" data-target-input="#input-password"><i class="fa-light fa-eye"></i></button>
                <button id="save-password" class="btn btn-outline-secondary" type="button"
                        data-route="{{ route('cabinet.password', $user) }}"
                >Сохранить</button>
            </div>
        </div>
        <div id="new-password" class="fs-7 text-success mt-1" style="display:none;">Пароль был изменен</div>
    </div>
@endsection

