@php
    use App\Modules\Shop\Application\DTOs\Client\ClientInfoData;
    /** @var ClientInfoData $client */

$noAddress = is_null($client->address->regionCode);
@endphp
<div class="box-card">
    <div>Доставка</div>

    {{-- Тип получения: самовывоз или доставка --}}
    <div class="mt-2" id="pickup-block">
        <span class="address-delivery--title">Способ получения: </span>
        <span class="data-view" id="data-view-is-pickup">{{ $client->isPickup ? 'Самовывоз' : 'Доставка' }}</span>
        <div class="edit-group" style="display:none;">
            <select class="form-select" name="isPickup" id="input-is-pickup" style="max-width:200px;">
                <option value="1" {{ $client->isPickup ? 'selected' : '' }}>Самовывоз</option>
                <option value="0" {{ !$client->isPickup ? 'selected' : '' }}>Доставка</option>
            </select>
        </div>
    </div>

    <div class="block-delivery">

        {{-- Адрес доставки (local + region) --}}
        <div class="delivery-local mt-3" id="delivery-address" {!! !$client->isPickup ? '' : ' style="display: none"' !!}>
            <span class="address-delivery--title">Адрес доставки: </span>
            <span class="data-view" id="data-view-address">{{ $client->address->getFullAddress() ?: 'Не указан' }}</span>
            <div class="edit-group" style="display:none;">
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="country" id="input-country"
                           placeholder="Страна" value="{{ $client->address->country }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <select class="form-select" name="region" id="input-region">
                        <option value="">-- Выберите регион --</option>
                    </select>
                    <input type="text" class="form-control" name="regionCode" id="input-region-code"
                           value="{{ $client->address->regionCode }}" readonly style="max-width:100px;"
                           placeholder="Код">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="city" id="input-city"
                           placeholder="Город" value="{{ $client->address->city }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="street" id="input-street"
                           placeholder="Улица, дом, квартира" value="{{ $client->address->street }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="postalCode" id="input-postal-code"
                           placeholder="Почтовый индекс" value="{{ $client->address->postalCode }}" autocomplete="off">
                </div>
            </div>

            <input type="hidden" name="address" id="input-address-hidden"
                   value="{{ $client->address->getFullAddress() }}">
        </div>
    </div>

    {{-- Контактные данные --}}
    <div class="mt-4" id="personal-order">
        <div>Контактные данные</div>

        {{-- ФИО --}}
        <div class="fullname-block mt-3">
            <span class="address-delivery--title">Получатель: </span>
            <span class="data-view" id="data-view-fullname">{{ $client->fullName->getValue() }}</span>
            <div class="edit-group" style="display:none;">
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="lastName" id="input-lastname"
                           placeholder="Фамилия" value="{{ $client->fullName->getLastName() }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="firstName" id="input-firstname"
                           placeholder="Имя" value="{{ $client->fullName->getFirstName() }}" autocomplete="off">
                </div>
                <div class="input-group mb-1">
                    <input type="text" class="form-control" name="middleName" id="input-middlename"
                           placeholder="Отчество" value="{{ $client->fullName->getMiddleName() }}" autocomplete="off">
                </div>
            </div>
        </div>

        {{-- Телефон --}}
        <div class="phone-block mt-3">
            <span class="address-delivery--title">Телефон: </span>
            <span class="data-view" id="data-view-phone">{{ $client->phone->getValue() }}</span>
            <div class="edit-group" style="display:none;">
                <input type="text" class="form-control mask-phone" id="input-phone" name="phone"
                       placeholder="Телефон" value="{{ $client->phone->getValue() }}" autocomplete="off">
            </div>
        </div>

        {{-- Email --}}
        <div class="mt-3">
            <span class="address-delivery--title">Email: </span>
            <span class="data-view" id="data-view-email">{{ $client->email }}</span>
            <div class="edit-group" style="display:none;">
                <input type="email" class="form-control" id="input-email-notify" name="email"
                       placeholder="Email" value="{{ $client->email }}" autocomplete="off">
            </div>
        </div>

        {{-- Скрытые поля для формы заказа --}}
        <input type="hidden" name="fullname" id="input-fullname-hidden" value="{{ $client->fullName->getValue() }}">
        <input type="hidden" name="phone" id="input-phone-hidden" value="{{ $client->phone->getValue() }}">
    </div>

    {{-- Кнопки (единые для всего блока) --}}
    <div class="mt-3"><button id="change-personal" class="btn btn-outline-primary address-delivery--change">Изменить</button>
        <button id="save-personal" class="btn btn-outline-secondary" type="button"
                data-route="{{ route('client.update-profile') }}" style="display:none;">Сохранить</button>
        <button id="cancel-personal" class="btn btn-outline-danger" type="button" style="display:none;">Отмена</button>
    </div>

    <div class="mt-4 fs-8">* Персональные данные необходимы для уточнения заказа и при получении товара для идентификации покупателя</div>
</div>
