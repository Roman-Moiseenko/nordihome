@php
    use App\Modules\Auth\Application\DTOs\Client\ClientInfoWebData;use App\Modules\Catalog\Domain\ValueObjects\PriceType;
    /** @var ClientInfoWebData $client */
@endphp

@extends('shop.cabinet.cabinet')

@section('title', 'Мой кабинет - NORDI HOME')

@section('h1', 'Мой кабинет')

@section('subcontent')
    <h3 class="mt-1">Персональные данные</h3>
    <div class="box-card view-option" id="personal-data">

        {{-- Фамилия Имя Отчество --}}
        <div class="field mt-3">
            <span class="label">Фамилия Имя Отчество:</span>
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

        {{-- Email уведомлений --}}
        <div class="field mt-3">
            <span class="label">Email уведомлений:</span>
            <span class="data-view" id="data-view-email">{{ $client->email }}</span>
            <div class="edit-group" style="display:none;">
                <input type="email" class="form-control" id="input-email-notify" name="email" placeholder="Email"
                       value="{{ $client->email }}" autocomplete="off">
            </div>
        </div>

        {{-- Контактный телефон --}}
        <div class="field mt-3">
            <span class="label">Контактный телефон:</span>
            <span class="data-view" id="data-view-phone">{{ $client->phone->getValue() }}</span>
            <div class="edit-group" style="display:none;">
                <input type="text" class="form-control" id="input-phone" name="phone" placeholder="Телефон"
                       value="{{ $client->phone->getValue() }}" autocomplete="off">
            </div>
        </div>

        {{-- Адрес доставки --}}
        <div class="field mt-3">
            <span class="label">Адрес доставки:</span>
            <span class="data-view" id="data-view-address">{{ $client->address->getFullAddress() }}</span>
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
        </div>

        {{-- Пол --}}
        <div class="field mt-3">
            <span class="label">Пол:</span>
            <span class="data-view" id="data-view-gender">
                @if($client->gender && $client->gender->getValue() === 'male')
                    Мужской
                @elseif($client->gender && $client->gender->getValue() === 'female')
                    Женский
                @else
                    Не указан
                @endif
            </span>
            <div class="edit-group" style="display:none;">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender-male" value="male"
                           @if($client->gender && $client->gender->getValue() === 'male') checked @endif>
                    <label class="form-check-label" for="gender-male">М</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender-female" value="female"
                           @if($client->gender && $client->gender->getValue() === 'female') checked @endif>
                    <label class="form-check-label" for="gender-female">Ж</label>
                </div>
            </div>
        </div>


        {{-- Тип цены (только чтение) --}}
        @if($client->priceType != PriceType::retail())
            <div class="field mt-3">
                <span class="label">Тип цены:</span>
                <span class="data-view">{{ $client->priceType->value }}</span>
            </div>
        @endif
        {{-- Скидка (только чтение) --}}
        @if($client->discount > 0)
            <div class="field mt-3">
                <span class="label">Скидка:</span>
                <span class="data-view">{{ $client->discount }}%</span>
            </div>
        @endif

        {{-- Согласие на обработку данных (только чтение) --}}
        <div class="field mt-3">
            <span class="label">Согласие на обработку данных:</span>
            <span class="data-view">
                @if($client->consent)
                    Дано {{ $client->consent->consentedAt->format('d.m.Y') }},
                    версия {{ $client->consent->policyVersion }}
                    @if(!$client->consent->active)
                        (отозвано)
                    @endif
                @else
                    Не дано
                @endif
            </span>
        </div>

        {{-- Кнопки управления --}}
        <div class="mt-3">
            <button id="change-personal" class="btn btn-outline-primary">Изменить</button>
            <button id="save-personal" class="btn btn-outline-secondary" type="button"
                    data-route="{{ route('client.update-profile') }}" style="display:none;">Сохранить
            </button>
            <button id="cancel-personal" class="btn btn-outline-danger" type="button" style="display:none;">Отмена
            </button>
        </div>

    </div>

    <h3 class="mt-1">Данные для входа</h3>
    <div class="box-card view-option">
        <div class="field mt-3">
            <span class="label">Email для входа:</span>
            <span id="data-email" class="data">{{ $client->loginEmail }}</span>
            <div id="group-email" class="input-group" style="display: none">
                <input type="text" class="form-control" id="input-email" aria-describedby="Email получателя"
                       placeholder="Email" autocomplete="off">
                <button id="save-email" class="btn btn-outline-secondary" type="button"
                        data-route="{{ route('cabinet.email', $client->id) }}">Сохранить
                </button>
            </div>
            <button id="change-email" class="change btn btn-outline-primary">Изменить</button>
        </div>
        <div class="inform mt-1 fs-87 text-danger">* После смены электронной почты кабинет будет недоступен, пока вы не
            подтвердите новую почту
        </div>
        <div class="password mt-3">
            <button id="change-password" class="btn btn-outline-primary">Сменить пароль</button>
            <div id="group-password" class="input-group" style="display: none">
                <input type="password" class="form-control" name="password" id="input-password" placeholder="Пароль"
                       minlength="6" required autocomplete="on" aria-describedby="show-hide-password">
                <button id="show-hide-password" class="btn btn-secondary" type="button"
                        data-target-input="#input-password"><i class="fa-light fa-eye"></i></button>
                <button id="save-password" class="btn btn-outline-secondary" type="button"
                        data-route="{{ route('cabinet.password', $client->id) }}">Сохранить
                </button>
            </div>
        </div>
        <div id="new-password" class="fs-7 text-success mt-1" style="display:none;">Пароль был изменен</div>
    </div>
@endsection

