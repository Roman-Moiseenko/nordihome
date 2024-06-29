<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">Габариты и вес товара в собранном виде, без упаковочного материала
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="flex">
                <div>
                    <select id="input-dimensions-measure" name="dimensions-measure" class="form-select w-full lg:w-40"
                            wire:model="measure" wire:change="save" wire:loading.attr="disabled">
                        @foreach(\App\Modules\Base\Entity\Dimensions::MEASURES as $measure)
                        <option value="{{ $measure }}">{{ $measure }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                    <input id="input-dimensions-weight" type="text" name="dimensions-weight" class="form-control " placeholder="Вес"
                           wire:model="weight" wire:change="save" wire:loading.attr="disabled">
                    <div class="form-help text-right">Вес</div>
                </div>
            </div>
            <div class="flex mt-3">
                <div>
                    <select id="input-dimensions-type" name="dimensions-type" class="form-select w-full lg:w-40"
                            wire:model="type" wire:change="save" wire:loading.attr="disabled">
                        @foreach(App\Modules\Base\Entity\Dimensions::TYPES as $type => $name)
                            <option value="{{ $type }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                    <input id="input-dimensions-height" type="text" name="dimensions-height" class="form-control"
                           placeholder="{{ $product->dimensions->nameZ() }}"
                           wire:model="height" wire:change="save" wire:loading.attr="disabled">
                    <div class="form-help text-right">{{ $product->dimensions->nameZ() }} (см)</div>
                </div>

                <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                    <input id="input-dimensions-width" type="text" name="dimensions-width" class="form-control"
                           placeholder="{{ $product->dimensions->nameX() }}"
                           wire:model="width" wire:change="save" wire:loading.attr="disabled">
                    <div class="form-help text-right">{{ $product->dimensions->nameX() }} (см)</div>
                </div>

                @if($product->dimensions->notDiameter())
                    <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                        <input id="input-dimensions-depth" type="text" name="dimensions-depth" class="form-control"
                               placeholder="{{ $product->dimensions->nameY() }}"
                               wire:model="depth" wire:change="save" wire:loading.attr="disabled">
                        <div class="form-help text-right">{{ $product->dimensions->nameY() }} (см)</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="grid grid-cols-12 gap-x-6 mt-5">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">Возможность доставки настраивается в модуле Доставка. <br>
            Для текущего товара можно только ограничить доставку, если габариты не позволяют это сделать.
        </div>
        <div class="col-span-12 lg:col-span-8">

            <div class="form-check form-switch ">
                <input id="checkbox-local" class="form-check-input" type="checkbox"
                      @if(!$delivery_local) disabled @endif
                       wire:model="local" wire:change="save" wire:loading.attr="disabled">
                <label class="form-check-label" for="checkbox-local">В пределах региона</label>
            </div>
            <div class="form-check form-switch mt-3">
                <input id="checkbox-delivery" class="form-check-input" type="checkbox" name="delivery"
                       @if(!$delivery_all) disabled @endif
                       wire:model="delivery" wire:change="save" wire:loading.attr="disabled">
                <label class="form-check-label" for="checkbox-delivery">Транспортной компанией </label>
            </div>
        </div>
    </div>
</div>
