<div>
    <div>
        <span class="mt-3 font-medium">Информация о заказе</span>
        <span>
            @if(is_null($order->getManager()))
                Менеджер не назначен
            @else
                менеджер: {{ $order->getManager()->fullname->getShortName() }}
            @endif
        </span>
        <button type="button" class="btn btn-outline-primary p-1" wire:click="toggle_fields" @if($show) style="transform:rotate(180deg);" @endif>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="chevron-down" class="lucide lucide-chevron-down stroke-1.5 h-4 w-4"><path d="m6 9 6 6 6-6"></path></svg>
        </button>
    </div>

    <div class="box p-3 flex flex-col items-center lg:items-start mt-2" @if(!$show) style="display:none;" @endif>

        <div class="truncate sm:whitespace-normal flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="contact" class="lucide lucide-contact stroke-1.5 w-4 h-4"><path d="M17 18a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><circle cx="12" cy="10" r="2"></circle><line x1="8" x2="8" y1="2" y2="4"></line><line x1="16" x2="16" y1="2" y2="4"></line></svg>
            &nbsp;
            @if(is_null($order->getManager()))
                Менеджер не назначен
            @else
            {{ $order->getManager()->fullname->getFullName() }} - менеджер
            @endif
        </div>
        <div class="flex mt-3">
            <div class="">
                <span>Общий вес груза </span><span class="font-medium" id="weight">{{ $order->getWeight() }} кг</span>
            </div>
            <div class="ml-3">
                <span>Общий объем груза </span><span class="font-medium" id="volume">{{ $order->getVolume() }} м3</span>
            </div>
        </div>
        <div class="form-control mt-4">
            <label class="form-check-label" for="order-comment">Комментарий</label>
            <input class="form-control" type="text" autocomplete="off"
                   wire:change="set_comment" wire:model="comment" wire:loading.attr="disabled"  @if(!$edit) disabled readonly @endif
            >
        </div>
    </div>
</div>
