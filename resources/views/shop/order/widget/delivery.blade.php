<div class="box-card">
    <div>Доставка</div>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_storage" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_STORAGE }}"
        {{ $user->isStorage() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_storage">Самовывоз</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_local" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_LOCAL }}"
        {{ $user->isLocal() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_local">Доставка по региону</label>
    <input type="radio" class="btn-check" name="delivery" data-state="change" id="delivery_region" autocomplete="off"
           value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_REGION }}"
        {{ $user->isRegion() ? 'checked' : '' }}
    >
    <label class="btn btn-outline-secondary" for="delivery_region">Транспортной компанией</label>

    <div class="block-delivery">
        <div class="delivery-storage mt-3 p-3" {!! $user->isStorage() ? '' : ' style="display: none"' !!}>
            @foreach($storages as $storage)
                <div class="checkbox-group">
                    <input type="radio" class="form-check-inline" name="storage" data-state="change" id="{{ $storage->slug }}" autocomplete="off"
                           value="{{ $storage->id }}"
                        {{ $user->StorageDefault() == $storage->id ? 'checked' : '' }}
                    >
                    <label for="{{ $storage->slug }}">{{ $storage->address }}</label>
                </div>
            @endforeach
        </div>
        <div class="delivery-local mt-3 p-3" {!! $user->isLocal() ? '' : ' style="display: none"' !!}>
            <div {!! $user->address->address != '' ? '' : ' style="display: none"' !!}>
                <span class="address-delivery--title">Адрес доставки: </span>
                <span class="address-delivery--info"> {{ $user->address->address }} </span>
                <span class="address-delivery--change" for="d---1">Изменить</span>
                <input type="hidden" name="address-local" id="input-delivery-local-hidden" value="{{ $user->address->address }}">
                <input type="hidden" name="latitude-local" value="{{ $user->address->latitude }}">
                <input type="hidden" name="longitude-local" value="{{ $user->address->longitude }}">
                <input type="hidden" name="post-local" value="{{ $user->address->post }}">
            </div>
            <div class="input-group" id="d---1" {!! $user->address->address == '' ? '' : ' style="display: none"' !!}>
                <input type="text" class="form-control" id="input-delivery-local" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-local" to="input-delivery-local-hidden">Сохранить</button>
            </div>
        </div>
        <div class="delivery-region" {!! $user->isRegion() ? '' : ' style="display: none"' !!}>
            <div id="slider-delivery-company" class="owl-carousel owl-theme mt-3 p-3">
                @foreach($companies as $i => $company)
                    <label class="radio-img">
                        <input type="radio" name="company" data-state="change" value="{{ $company['class'] }}">
                        <img src="{{ $company['image'] }}" alt="{{ $company['name'] }}" title="{{ $company['name'] }}">
                    </label>
                @endforeach
            </div>
            <div {!! $user->address->address != '' ? '' : ' style="display: none"' !!}>
                <span class="address-delivery--title">Адрес доставки: </span>
                <span class="address-delivery--info"> {{ $user->address->address }} </span>
                <span class="address-delivery--change" for="d---2">Изменить</span>
                <input type="hidden" name="address-region" id="input-delivery-region-hidden" value="{{ $user->address->address }}">
                <input type="hidden" name="latitude-region" value="{{ $user->address->latitude }}">
                <input type="hidden" name="longitude-region" value="{{ $user->address->longitude }}">
                <input type="hidden" name="post-region" value="{{ $user->address->post }}">
            </div>
            <div class="input-group" id="d---2" {!! $user->address->address == '' ? '' : ' style="display: none"' !!}>
                <input type="text" class="form-control" id="input-delivery-region" aria-describedby="emailHelp" placeholder="Начните вводить адрес">
                <button class="btn btn-outline-secondary input-to-hidden" type="button" from="input-delivery-region" to="input-delivery-region-hidden">Сохранить</button>
            </div>
        </div>
    </div>
</div>
