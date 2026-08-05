@php
    use App\Modules\Shop\Application\DTOs\Client\ClientInfoData;
    /** @var ClientInfoData $client */
$isStorage = is_null($client->address->regionCode);
$isLocal = $client->address->regionCode == 39;
$isRegion = !$isStorage && !$isLocal;
$noAddress = is_null($client->address->regionCode);
@endphp
<div class="box-card">
    <div>Доставка</div>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_storage" autocomplete="off"
           value="{{ true }}"
        {{ $client->isPickup ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_storage">Самовывоз</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_local" autocomplete="off"
           value="{{ false }}"
        {{ !$client->isPickup ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_local">Доставка</label>

    <div class="block-delivery">
        <div class="delivery-storage mt-3 p-3" {!! $isStorage ? '' : ' style="display: none"' !!}>
            @foreach($storages as $storage)
                <div class="checkbox-group">
                    <input type="radio" class="form-check-inline" name="storage" data-state="change"
                           id="{{ $storage->slug }}" autocomplete="off"
                           value="{{ $storage->id }}"
                    >
                    <label for="{{ $storage->slug }}">{{ $storage->address }}</label>
                </div>
            @endforeach
        </div>

        {{-- Адрес доставки (local + region) --}}
        <div class="delivery-local mt-3 p-3" id="delivery-address" {!! !$client->isPickup ? '' : ' style="display: none"' !!}>
            <span class="address-delivery--title">Адрес доставки: </span>
            <span class="data-view" {!! $noAddress ? ' style="display:none;"' : '' !!}>{{ $client->address->getFullAddress() }}</span>
            <div class="edit-group" {!! $noAddress ? '' : ' style="display:none;"' !!}>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="country" id="input-delivery-country"
                           placeholder="Страна" value="{{ $client->address->country }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <select class="form-select" name="region" id="input-delivery-region">
                        <option value="">-- Выберите регион --</option>
                    </select>
                    <input type="text" class="form-control" name="regionCode" id="input-delivery-region-code"
                           value="{{ $client->address->regionCode }}" readonly style="max-width:100px;"
                           placeholder="Код">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="city" id="input-delivery-city"
                           placeholder="Город" value="{{ $client->address->city }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="street" id="input-delivery-street"
                           placeholder="Улица, дом, квартира" value="{{ $client->address->street }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="postalCode" id="input-delivery-postal-code"
                           placeholder="Почтовый индекс" value="{{ $client->address->postalCode }}" autocomplete="off">
                </div>
                <div class="mt-2">
                    <button id="save-delivery-address" class="btn btn-outline-secondary" type="button"
                            data-route="{{ route('client.update-profile') }}">Сохранить</button>
                    <button id="cancel-delivery-address" class="btn btn-outline-danger" type="button" {!! $noAddress ? ' style="display:none;"' : '' !!}>Отмена</button>
                </div>
            </div>
            <button id="change-delivery-address" class="btn btn-outline-primary address-delivery--change" {!! $noAddress ? ' style="display:none;"' : '' !!}>Изменить</button>

            <input type="hidden" name="address-local" id="input-delivery-local-hidden"
                   value="{{ $client->address->getFullAddress() }}">
            <input type="hidden" name="latitude-local" value="{{ $client->address->latitude ?? '' }}">
            <input type="hidden" name="longitude-local" value="{{ $client->address->longitude ?? '' }}">
            <input type="hidden" name="post-local" value="{{ $client->address->post ?? '' }}">
            <input type="hidden" name="address-region" id="input-delivery-region-hidden"
                   value="{{ $client->address->getFullAddress() }}">
            <input type="hidden" name="latitude-region" value="{{ $client->address->latitude ?? '' }}">
            <input type="hidden" name="longitude-region" value="{{ $client->address->longitude ?? '' }}">
            <input type="hidden" name="post-region" value="{{ $client->address->post ?? '' }}">
        </div>
    </div>
</div>
<div class="box-card" id="personal-order">
    <div>Контактные данные *</div>

    {{-- ФИО --}}
    <div class="fullname-block mt-3">
        <span class="address-delivery--title">Получатель: </span>
        <span class="data-view">{{ $client->fullName->getValue() }}</span>
        <div class="edit-group" style="display:none;">
            <div class="input-group mb-1">
                <input type="text" class="form-control" name="lastName" id="input-order-lastname"
                       placeholder="Фамилия" value="{{ $client->fullName->getLastName() }}" autocomplete="off">
            </div>
            <div class="input-group mb-1">
                <input type="text" class="form-control" name="firstName" id="input-order-firstname"
                       placeholder="Имя" value="{{ $client->fullName->getFirstName() }}" autocomplete="off">
            </div>
            <div class="input-group mb-1">
                <input type="text" class="form-control" name="middleName" id="input-order-middlename"
                       placeholder="Отчество" value="{{ $client->fullName->getMiddleName() }}" autocomplete="off">
            </div>
        </div>
    </div>

    {{-- Телефон --}}
    <div class="phone-block mt-3">
        <span class="address-delivery--title">Телефон: </span>
        <span class="data-view">{{ $client->phone->getValue() }}</span>
        <div class="edit-group" style="display:none;">
            <input type="text" class="form-control mask-phone" id="input-order-phone" name="phone"
                   placeholder="Телефон" value="{{ $client->phone->getValue() }}" autocomplete="off">
        </div>
    </div>

    {{-- Email --}}
    <div class="mt-3">
        <span class="address-delivery--title">Email: </span>
        <span class="data-view">{{ $client->email }}</span>
        <div class="edit-group" style="display:none;">
            <input type="email" class="form-control" id="input-order-email" name="email"
                   placeholder="Email" value="{{ $client->email }}" autocomplete="off">
        </div>
    </div>

    {{-- Кнопки --}}
    <div class="mt-3">
        <button id="change-order-personal" class="btn btn-outline-primary address-delivery--change">Изменить</button>
        <button id="save-order-personal" class="btn btn-outline-secondary" type="button"
                data-route="{{ route('client.update-profile') }}" style="display:none;">Сохранить</button>
        <button id="cancel-order-personal" class="btn btn-outline-danger" type="button" style="display:none;">Отмена</button>
    </div>

    {{-- Скрытые поля для формы заказа --}}
    <input type="hidden" name="fullname" id="input-fullname-hidden" value="{{ $client->fullName->getValue() }}">
    <input type="hidden" name="phone" id="input-phone-hidden" value="{{ $client->phone->getValue() }}">

    <div class="mt-4 fs-8">* Персональные данные необходимы для уточнения заказа и при получении товара для идентификации покупателя</div>
</div>
