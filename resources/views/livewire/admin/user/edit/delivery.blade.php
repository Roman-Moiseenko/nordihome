<div>
    <div class="items-center w-100 mr-2" @if(!$change) style="display: none" @endif>
        <div class="ml-2">
            <div class="form-check">
                <input id="delivery_storage" class="form-check-input mr-1" wire:model="delivery" type="radio"
                       value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_STORAGE }}">
                <label for="delivery_storage">Самовывоз</label>
            </div>
            <div class="form-check">
                <input id="delivery_local" class="form-check-input mr-1" wire:model="delivery" type="radio"
                       value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_LOCAL }}">
                <label for="delivery_local">По области</label>
            </div>
            <div class="form-check">
                <input id="delivery_region" class="form-check-input mr-1" wire:model="delivery" type="radio"
                       value="{{ \App\Modules\Order\Entity\Order\OrderExpense::DELIVERY_REGION }}">
                <label for="delivery_region">По РФ</label>
            </div>
        </div>

        <div class="flex mb-1">
            <input class="form-control ml-2 mt-1" wire:model="address" placeholder="Адрес клиента" autocomplete="off">
            <button class="btn btn-success-soft btn-sm ml-1 mt-1" wire:click="save_change">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-save">
                    <path
                        d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/>
                    <path d="M7 3v4a1 1 0 0 0 1 1h7"/>
                </svg>
            </button>
            <button class="btn btn-secondary btn-sm ml-1 mt-1" wire:click="close_change">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-x">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
    </div>
    <div class="flex items-center" @if($change) style="display: none" @endif>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="map"
             class="lucide lucide-map stroke-1.5 w-4 h-4">
            <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon>
            <line x1="9" x2="9" y1="3" y2="18"></line>
            <line x1="15" x2="15" y1="6" y2="21"></line>
        </svg>
        <span class="ml-2">{{ $html_delivery }}</span>
        @if($edit)
            <button class="btn btn-warning-soft btn-sm ml-1" wire:click="open_change">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     data-lucide="pencil" class="lucide lucide-pencil stroke-1.5 w-4 h-4">
                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                    <path d="m15 5 4 4"></path>
                </svg>
            </button>
        @endif
    </div>
</div>
