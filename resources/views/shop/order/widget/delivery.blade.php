<div class="box-card">
    <div>Доставка</div>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_storage" autocomplete="off"
           value="{{ \App\Modules\Delivery\Entity\DeliveryOrder::STORAGE }}"
        {{ $default->delivery->isStorage() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_storage">Самовывоз</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_local" autocomplete="off"
           value="{{ \App\Modules\Delivery\Entity\DeliveryOrder::LOCAL }}"
        {{ $default->delivery->isLocal() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_local">Доставка по региону</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_region" autocomplete="off"
           value="{{ \App\Modules\Delivery\Entity\DeliveryOrder::REGION }}"
        {{ $default->delivery->isRegion() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_region">Транспортной компанией</label>

    <div class="block-delivery">
        <div class="delivery-storage mt-3 p-3" {!! $default->delivery->isStorage() ? '' : ' style="display: none"' !!}>
            @foreach($storages as $storage)
                <div class="checkbox-group">
                    <input type="radio" class="form-check-inline" name="storage" data-state="change" id="{{ $storage->slug }}" autocomplete="off"
                           value="{{ $storage->id }}"
                        {{ $default->delivery->storage == $storage->id ? 'checked' : '' }}
                    >
                    <label for="{{ $storage->slug }}">{{ $storage->address }}</label>
                </div>
            @endforeach
        </div>
        <div class="delivery-local mt-3 p-3" {!! $default->delivery->isLocal() ? '' : ' style="display: none"' !!}>
            <div {!! $default->delivery->local->address != '' ? '' : ' style="display: none"' !!}>
                <span class="address-delivery--title">Адрес доставки: </span>
                <span class="address-delivery--info"> {{ $default->delivery->local->address }} </span>
                <span class="address-delivery--change" for="d---1">Изменить</span>
                <input type="hidden" name="address-local" id="input-delivery-local-hidden" value="{{ $default->delivery->local->address }}">
                <input type="hidden" name="latitude-local" value="{{ $default->delivery->local->latitude }}">
                <input type="hidden" name="longitude-local" value="{{ $default->delivery->local->longitude }}">
                <input type="hidden" name="post-local" value="{{ $default->delivery->local->post }}">
            </div>
            <div class="input-group" id="d---1" {!! $default->delivery->local->address == '' ? '' : ' style="display: none"' !!}>
                <input type="text" class="form-control" id="input-delivery-local" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-local" to="input-delivery-local-hidden">Сохранить</button>
            </div>
        </div>
        <div class="delivery-region" {!! $default->delivery->isRegion() ? '' : ' style="display: none"' !!}>
            <div id="slider-delivery-company" class="owl-carousel owl-theme mt-3 p-3">
                @foreach($companies as $i => $company)
                    <label class="radio-img">
                        <input type="radio" name="company" data-state="change" value="{{ $company['class'] }}"
                            {{ ($company['class'] == $default->delivery->company) ? 'checked' : '' }}>
                        <img src="{{ $company['image'] }}" alt="{{ $company['name'] }}" title="{{ $company['name'] }}">
                    </label>
                @endforeach
            </div>
            <div {!! $default->delivery->region->address != '' ? '' : ' style="display: none"' !!}>
                <span class="address-delivery--title">Адрес доставки: </span>
                <span class="address-delivery--info"> {{ $default->delivery->region->address }} </span>
                <span class="address-delivery--change" for="d---2">Изменить</span>
                <input type="hidden" name="address-region" id="input-delivery-region-hidden" value="{{ $default->delivery->region->address }}">
                <input type="hidden" name="latitude-region" value="{{ $default->delivery->region->latitude }}">
                <input type="hidden" name="longitude-region" value="{{ $default->delivery->region->longitude }}">
                <input type="hidden" name="post-region" value="{{ $default->delivery->region->post }}">
            </div>
            <div class="input-group" id="d---2" {!! $default->delivery->region->address == '' ? '' : ' style="display: none"' !!}>
                <input type="text" class="form-control" id="input-delivery-region" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-region" to="input-delivery-region-hidden">Сохранить</button>
            </div>
        </div>
    </div>
</div>
