<div>
    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-12 lg:col-span-4 lg:mr-20">
            Габариты товара в собранном виде
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="flex">
                <div>
                    <select id="input-dimensions-type" class="form-select w-full lg:w-40"
                            wire:model="type" wire:change="save" wire:loading.attr="disabled">
                        @foreach(App\Modules\Base\Entity\Dimensions::TYPES as $type => $name)
                            <option value="{{ $type }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                    <input id="input-dimensions-height" type="text" class="form-control"
                           placeholder="{{ $product->dimensions->nameZ() }}"
                           wire:model="height" wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                    <div class="form-help text-right">{{ $product->dimensions->nameZ() }} (см)</div>
                </div>

                <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                    <input id="input-dimensions-width" type="text" class="form-control"
                           placeholder="{{ $product->dimensions->nameX() }}" autocomplete="off"
                           wire:model="width" wire:change="save" wire:loading.attr="disabled">
                    <div class="form-help text-right">{{ $product->dimensions->nameX() }} (см)</div>
                </div>

                @if($product->dimensions->notDiameter())
                    <div class="input-form ml-0 w-full lg:ml-4 lg:w-40 ">
                        <input id="input-dimensions-depth" type="text" class="form-control" autocomplete="off"
                               placeholder="{{ $product->dimensions->nameY() }}"
                               wire:model="depth" wire:change="save" wire:loading.attr="disabled">
                        <div class="form-help text-right">{{ $product->dimensions->nameY() }} (см)</div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-span-12 lg:col-span-4 lg:mr-20 mt-3">
            Габариты и вес упакованного товара
            <x-base.button class="w-full mt-4" variant="primary" type="button" wire:click="add" wire:loading.attr="disabled" wire:ignore>
                <x-base.lucide class="mr-2" icon="weight"/>
                Добавить
            </x-base.button>
        </div>
        <div class="col-span-12 lg:col-span-8 mt-3">
            @foreach($packages as $key => $package)
                <div class="flex mt-2">
                    <div class="input-form ml-0 w-full lg:w-20 ">
                        <input id="input-dimensions-height" type="text" class="form-control"
                               placeholder=""
                               wire:model="packages.{{ $key }}.height"
                                wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                        <div class="form-help text-right">Высота (см)</div>
                    </div>

                    <div class="input-form ml-0 w-full lg:ml-4 lg:w-20 ">
                        <input id="input-dimensions-height" type="text" class="form-control"
                               placeholder=""
                               wire:model="packages.{{ $key }}.width"
                               wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                        <div class="form-help text-right">Ширина (см)</div>
                    </div>

                    <div class="input-form ml-0 w-full lg:ml-4 lg:w-20 ">
                        <input id="input-dimensions-height" type="text" class="form-control"
                               placeholder=""
                               wire:model="packages.{{ $key }}.length"
                               wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                        <div class="form-help text-right">Длина (см)</div>
                    </div>
                    <div class="input-form ml-0 w-full lg:ml-4 lg:w-20 ">
                        <input id="input-dimensions-height" type="text" class="form-control"
                               placeholder=""
                               wire:model="packages.{{ $key }}.weight"
                               wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                        <div class="form-help text-right">Вес (кг)</div>
                    </div>
                    <div class="input-form ml-0 w-full lg:ml-4 lg:w-20 ">
                        <input id="input-dimensions-height" type="text" class="form-control"
                               placeholder=""
                               wire:model="packages.{{ $key }}.quantity"
                               wire:change="save" wire:loading.attr="disabled" autocomplete="off">
                        <div class="form-help text-right">Кол-во</div>
                    </div>
                    <div class="lg:ml-4">
                        <button class="btn btn-outline-danger" type="button" wire:click="remove({{$key}})" wire:loading.attr="disabled" wire:ignore>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="trash-2" class="lucide lucide-trash-2 stroke-1.5 w-4 h-4"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                        </button>
                    </div>

                </div>
            @endforeach
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
                <input id="checkbox-delivery" class="form-check-input" type="checkbox"
                       @if(!$delivery_all) disabled @endif
                       wire:model="delivery" wire:change="save" wire:loading.attr="disabled">
                <label class="form-check-label" for="checkbox-delivery">Транспортной компанией </label>
            </div>
        </div>
    </div>
</div>
