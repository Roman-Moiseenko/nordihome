<div>
    <h2 class=" mt-3 font-medium">Контакты клиенты</h2>
    <div class="box p-3 flex flex-row items-center flex-wrap lg:items-start mt-4">
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

                <button class="w-32 btn btn-primary shadow-md mr-2" type="button" wire:click="show_fields" @if($show) style="display:none;" @endif>
                    Клиент
                </button>

                <div class="flex" @if(!$show) style="display:none;" @endif>
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
                <div class="mt-3" @if(!$show) style="display:none;" @endif>
                    <button id="modal-cancel" class="mr-1 w-24 btn btn-outline-secondary" wire:click="close_fields" type="button">
                        Отмена
                    </button>
                    <button class=" btn btn-primary" type="button" wire:click="set_user">{{ $button_caption }}</button>
                </div>

            </div>
        @endif
    </div>
</div>
