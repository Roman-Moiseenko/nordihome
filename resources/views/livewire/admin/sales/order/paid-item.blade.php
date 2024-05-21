<div>
    <div class="box-in-box flex items-center p-2">
        <div class="w-10 text-center">{{ $i + 1 }}</div>
        <div class="w-32 text-center">{{ $item->product->code }}</div>
        <div class="w-1/4">
            <div>{{ $item->product->name }}  @if($item->preorder) <b>(предзаказ)</b> @endif</div>
            <div>{{ $item->product->dimensions->weight() }} кг | {{ $item->product->dimensions->volume() }} м3</div>
            <div class="font-medium"><em>{{ $item->comment }}</em></div>
        </div>
        <div class="w-32 text-center px-1">{{ price($item->sell_cost) }}</div>
        <div class="w-20 px-1 text-center">
            <input id="item-quantity-{{ $item->id }}" type="number" class="form-control text-center update-data-ajax"
                   min="1" max="{{ $item->getRemains() }}" {{ $item->getRemains() == 0 ? 'disabled' : '' }}
                   @if($item->preorder && is_null($item->reserve)) disabled @endif
                   value="{{ $item->getRemains() }}"
                   wire:change="set_quantity" wire:model="quantity" wire:loading.attr="disabled"
            >
        </div>

        <div class="text-center">
            @foreach($storages as $storage)
                @php
                    $storageItem = $storage->getItem($item->product);
                    $orderReserve = $item->getReserveByStorageItem($storageItem->id);
                @endphp
                <div class="flex items-center">
                    <div class="w-32">{{ $storage->name }}</div>
                    <div class="w-10 {{ ($item->getRemains() > $storageItem->quantity) ? 'text-danger' : '' }}">
                        {{ $storageItem->quantity }}
                    </div>
                    <div class="w-10">{{ $storageItem->getQuantityReserve($order->id) }}</div>
                    <div>
                        <input id="item-reserve-{{ '--' }}" type="text" class="form-control text-center w-10 p-1"
                               value="{{ (is_null($orderReserve)) ? 0 : $orderReserve->quantity }}" aria-describedby="input-quantity" disabled>
                    </div>
                    <div class="flex">
                        <button class="btn btn-sm" type="button" wire:click="reserve_up({{ $storage->id }}, 1)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="chevron-up" class="lucide lucide-chevron-up stroke-1.5 w-4 h-4 ml-2"><path d="m18 15-6-6-6 6"></path></svg>
                        </button>
                        <button class="btn btn-sm" type="button" wire:click="reserve_up({{ $storage->id }}, {{ $item->quantity }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="chevrons-up" class="lucide lucide-chevrons-up stroke-1.5 w-4 h-4 ml-2"><path d="m17 11-5-5-5 5"></path><path d="m17 18-5-5-5 5"></path></svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="w-20 text-center">
            <div class="form-check form-switch justify-center mt-3">
                <input id="item-{{ $item->id }}" class="form-check-input update-data-ajax" type="checkbox"
                       data-input="item-quantity-{{ $item->id }}" name="items"
                       @if($item->preorder && is_null($item->reserve) || ($item->getRemains() == 0)) disabled
                       @else checked @endif

                       wire:change="toggle_enabled" wire:model="enabled" wire:loading.attr="disabled"
                       value="{{ $item->id }}"
                >
                <label class="form-check-label" for="item-{{ $item->id }}"></label>
            </div>
        </div>
        <div class="ml-auto">
            @if($item->preorder)
                @if(is_null($item->supplyStack))
                    <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
                        <x-base.popover.button as="x-base.button" variant="dark" class="w-100"
                                               id="button-supply-stack" type="button">
                            В Заказ
                            <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                        </x-base.popover.button>
                        <x-base.popover.panel>
                            <form method="post" action="{{ route('admin.accounting.supply.add-stack', $item) }}">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <div class="p-2">
                                    <x-base.tom-select id="select-storage-supply" name="storage" class=""
                                                       data-placeholder="Выберите Склад поступления">
                                        <option value="0"></option>
                                        @foreach($storages as $storage)
                                            <option value="{{ $storage->id }}"
                                            >{{ $storage->name }}</option>
                                        @endforeach
                                    </x-base.tom-select>

                                    <div class="flex items-center mt-3">
                                        <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown"
                                                       variant="secondary" type="button">
                                            Отмена
                                        </x-base.button>
                                        <button id="create-supply-stack" class="w-32 ml-2 btn btn-primary" type="submit">
                                            Создать
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </x-base.popover.panel>
                    </x-base.popover>
                @else
                    <span>
                    @if(is_null($item->supplyStack->supply))
                            {{ $item->supplyStack->status() }}
                        @else
                            <a class="text-primary"
                               href="{{ route('admin.accounting.supply.show', $item->supplyStack->supply) }}"
                               target="_blank">{{ $item->supplyStack->status() }}</a>
                        @endif
                </span>
                @endif
            @endif
        </div>
    </div>


</div>
