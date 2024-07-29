<div>
    <h2 class=" mt-3 font-medium">Контакты клиенты</h2>
    <div class="box p-3 flex flex-row items-center flex-wrap lg:items-start mt-4">
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
    </div>
</div>
