<div class="box-in-box flex items-center p-2">
    <div class="w-20 text-center">{{ $i + 1 }}</div>
    <div class="w-1/4">
        <div>{{ $item->product->name }}  @if($item->preorder) <b>(предзаказ)</b> @endif</div>
        <div>{{ $item->product->dimensions->weight() }} кг | {{ $item->product->dimensions->volume() }} м3</div>
    </div>
    <div class="w-32 text-center px-1">{{ price($item->sell_cost) }}</div>
    <div class="w-20 px-1 text-center">
        <input id="item-quantity-{{ $item->id }}" type="number" class="form-control text-center update-data-ajax"
               value="{{ $item->getRemains() }}" aria-describedby="input-quantity"
               min="1" max="{{ $item->getRemains() }}" {{ $item->getRemains() == 0 ? 'disabled' : '' }}
        @if($item->preorder && is_null($item->reserve)) disabled @endif
        >
    </div>
    <div class="w-40 text-center">
        @foreach($item->product->getStorages() as $storage)
            <div class="{{ ($item->getRemains() > $storage->getQuantity($item->product)) ? 'text-danger' : '' }}">
                {{ $storage->getQuantity($item->product) . ' (' . $storage->name . ')' }}
            </div>
        @endforeach
    </div>
    <div class="w-20 text-center">
        <div class="form-check form-switch justify-center mt-3">
            <input id="item-{{ $item->id }}" class="form-check-input update-data-ajax"
                   data-input="item-quantity-{{ $item->id }}" type="checkbox" name="items" value="{{ $item->id }}"
                   @if($item->preorder && is_null($item->reserve) || ($item->getRemains() == 0)) disabled @else checked @endif
            >
            <label class="form-check-label" for="item-{{ $item->id }}"></label>
        </div>
    </div>
    <div class=" ml-auto">
        @if($item->preorder)
            @if(is_null($item->supplyStack))
                <x-base.popover class="inline-block mt-auto w-100" placement="bottom-start">
                    <x-base.popover.button as="x-base.button" variant="dark" class="w-100"
                                           id="button-supply-stack" type="button">
                        В Заказ Менеджеру
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
                                <x-base.button id="close-add-group" class="w-32 ml-auto" data-tw-dismiss="dropdown" variant="secondary" type="button">
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
                    <a class="text-primary" href="{{ route('admin.accounting.supply.show', $item->supplyStack->supply) }}" target="_blank">{{ $item->supplyStack->status() }}</a>
                    @endif
                </span>
            @endif
        @endif
    </div>
</div>

