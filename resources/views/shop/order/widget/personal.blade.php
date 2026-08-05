@php
    use App\Modules\Catalog\Domain\ValueObjects\PriceType;
    use App\Modules\Shop\Application\DTOs\Client\ClientInfoData;
    /** @var ClientInfoData $client */
@endphp
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

