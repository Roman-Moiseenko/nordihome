<div class="box-card">
    <div>Контактные данные *</div>
    <div class="fullname-block mt-3" {!! $default->delivery->fullname->surname != '' ? '' : ' style="display: none"' !!}>
        <span class="address-delivery--title">Получатель: </span>
        <span class="address-delivery--info"> {{ $default->delivery->fullname->getFullName() }} </span>
        <span class="address-delivery--change" for="d---3">Изменить</span>
        <input type="hidden" name="fullname" id="input-fullname-hidden" value="{{ $default->delivery->fullname->getFullName() }}">
    </div>
    <div class="input-group" id="d---3" {!! $default->delivery->fullname->surname == '' ? '' : ' style="display: none"' !!}>
        <input type="text" class="form-control" id="input-fullname" aria-describedby="Фамилия получателя" placeholder="Фамилия Имя Отчество">
        <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-fullname" to="input-fullname-hidden">Сохранить</button>
    </div>
    <div class="phone-block mt-3" {!! $default->phone != '' ? '' : ' style="display: none"' !!}>
        <span class="address-delivery--title">Телефон: </span>
        <span class="address-delivery--info"> {{ $default->phone }} </span>
        <span class="address-delivery--change" for="d---4">Изменить</span>
        <input type="hidden" name="phone" id="input-phone-hidden" value="{{ $default->phone }}">
    </div>
    <div class="input-group" id="d---4" {!! $default->phone == '' ? '' : ' style="display: none"' !!}>
        <input type="text" class="form-control" id="input-phone" aria-describedby="Телефон получателя" placeholder="Телефон">
        <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-phone" to="input-phone-hidden">Сохранить</button>
    </div>

    <div class="mt-4 fs-8">* Персональные данные необходимы для уточнения заказа и при получении товара для идентификации покупателя</div>
</div>
