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
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_STORAGE }}"
        {{ $isStorage ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_storage">Самовывоз</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_local" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_LOCAL }}"
        {{ $isLocal ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_local">Доставка по региону</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_region" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_REGION }}"
        {{ $isRegion ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_region">Транспортной компанией</label>

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
        <div class="delivery-local mt-3 p-3" id="delivery-address" {!! ($isLocal || $isRegion) ? '' : ' style="display: none"' !!}>
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
