<div>

    <div>
        <span class="mt-3 font-medium">Клиент</span> <span>{{ $order->user->getPublicName() }}</span>
        <button type="button" class="btn btn-outline-primary p-1" wire:click="toggle_fields" @if($show) style="transform:rotate(180deg);" @endif>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="chevron-down" class="lucide lucide-chevron-down stroke-1.5 h-4 w-4"><path d="m6 9 6 6 6-6"></path></svg>
        </button>
    </div>

    <div class="box p-3 flex flex-row items-center flex-wrap lg:items-start mt-4" @if(!$show) style="display:none;" @endif>
        @if(!is_null($order->user_id))
            <div class="truncate sm:whitespace-normal flex items-center my-auto">
                <livewire:admin.user.edit.fullname :user="$order->user" :edit="$edit"/>
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
                <livewire:admin.user.edit.email :user="$order->user" :edit="$edit"/>
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
                <livewire:admin.user.edit.phone :user="$order->user" :edit="$edit"/>
            </div>
            <div class="truncate sm:whitespace-normal  ml-4 my-auto">
                <livewire:admin.user.edit.delivery :user="$order->user" :edit="$edit"/>
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
                <livewire:admin.user.edit.payment :user="$order->user" :edit="$edit"/>
            </div>
        @else
            <div class="flex flex-col">
                <div class="flex">
                    <div>
                        <label class="inline-block mb-2" for="input-phone">Телефон</label>
                        <input id="input-phone" class="mask-phone form-control"
                               type="text" name="phone" placeholder="8 (___) ___-__-__"
                               required wire:model.lazy="phone"/>
                    </div>
                    <div class="">
                        <label class="inline-block mb-2" for="input-email">Почта</label>
                        <input id="input-email" class="mask-email form-control"
                               type="text" name="email" placeholder="example@gmail.com" required
                               wire:model.lazy="email"/>
                    </div>
                    <div class="">
                        <label class="inline-block mb-2" for="input-name">Имя</label>
                        <input id="input-name" class="form-control" type="text" name="name" placeholder="Имя"
                               wire:model="name"
                        />
                    </div>
                </div>
                <div class="mt-3">
                    <button id="modal-cancel" class="mr-1 w-24 btn btn-outline-secondary" wire:click="close_fields" type="button">
                        Отмена
                    </button>
                    <button class=" btn btn-primary" type="button" wire:click="set_user">{{ $button_caption }}</button>
                </div>

            </div>
        @endif
    </div>
</div>
