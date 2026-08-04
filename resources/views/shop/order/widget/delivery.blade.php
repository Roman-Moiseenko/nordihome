<div class="box-card">
    <div>Доставка</div>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_storage" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_STORAGE }}"
        {{ $client->isStorage() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_storage">Самовывоз</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_local" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_LOCAL }}"
        {{ $client->isLocal() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_local">Доставка по региону</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_region" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_REGION }}"
        {{ $client->isRegion() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_region">Транспортной компанией</label>

    <div class="block-delivery">
        <div class="delivery-storage mt-3 p-3" {!! $client->isStorage() ? '' : ' style="display: none"' !!}>
            @foreach($storages as $storage)
                <div class="checkbox-group">
                    <input type="radio" class="form-check-inline" name="storage" data-state="change" id="{{ $storage->slug }}" autocomplete="off"
                           value="{{ $storage->id }}"

                    >
                    <label for="{{ $storage->slug }}">{{ $storage->address }}</label>
                </div>
            @endforeach
        </div>
        <div class="delivery-local mt-3 p-3" {!! $client->isLocal() ? '' : ' style="display: none"' !!}>
            <div {!! $client->address->address != '' ? '' : ' style="display: none"' !!}>
                <span class="address-delivery--title">Адрес доставки: </span>
                <span class="address-delivery--info"> {{ $client->address->address }} </span>
                <span class="address-delivery--change" for="d---1">Изменить</span>
                <input type="hidden" name="address-local" id="input-delivery-local-hidden" value="{{ $client->address->address }}">
                <input type="hidden" name="latitude-local" value="{{ $client->address->latitude }}">
                <input type="hidden" name="longitude-local" value="{{ $client->address->longitude }}">
                <input type="hidden" name="post-local" value="{{ $client->address->post }}">
            </div>
            <div class="input-group" id="d---1" {!! $client->address->address == '' ? '' : ' style="display: none"' !!}>
                <input type="text" class="form-control" id="input-delivery-local"
                       aria-describedby="emailHelp" placeholder="Начните вводить адрес" autocomplete="off">
                <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-local" to="input-delivery-local-hidden">Сохранить</button>
            </div>
        </div>
        <div class="delivery-region" {!! $client->isRegion() ? '' : ' style="display: none"' !!}>
            <div id="slider-delivery-company" class="owl-carousel owl-theme mt-3 p-3">
                @foreach($companies as $i => $company)
                    <label class="radio-img">
                        <input type="radio" name="company" data-state="change" value="{{ $company['class'] }}">
                        <img src="{{ $company['image'] }}" alt="{{ $company['name'] }}" title="{{ $company['name'] }}">
                    </label>
                @endforeach
            </div>
                <div {!! $client->address->address != '' ? '' : ' style="display: none"' !!}>
                <span class="address-delivery--title">Адрес доставки: </span>
                <span class="address-delivery--info"> {{ $client->address->address }} </span>
                <span class="address-delivery--change" for="d---2">Изменить</span>
                <input type="hidden" name="address-region" id="input-delivery-region-hidden" value="{{ $client->address->address }}">
                <input type="hidden" name="latitude-region" value="{{ $client->address->latitude }}">
                <input type="hidden" name="longitude-region" value="{{ $client->address->longitude }}">
                <input type="hidden" name="post-region" value="{{ $client->address->post }}">
            </div>
            <div class="input-group" id="d---2" {!! $client->address->address == '' ? '' : ' style="display: none"' !!}>
                <input type="text" class="form-control" id="input-delivery-region" aria-describedby="emailHelp"
                       placeholder="Начните вводить адрес" autocomplete="off">
                <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-region" to="input-delivery-region-hidden">Сохранить</button>
            </div>
        </div>
    </div>
</div>
