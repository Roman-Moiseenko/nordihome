<div>
    <h2 class=" mt-3 font-medium">Контакты клиенты</h2>
    <div class="box p-3 flex flex-row items-center flex-wrap lg:items-start mt-4">
        <div class="truncate sm:whitespace-normal flex items-center my-auto">
            <x-base.lucide icon="user" class="w-4 h-4"/>&nbsp;
            <a href="{{ route('admin.users.show', $order->user) }}">{{ $order->userFullName() }}</a>
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
            <x-base.lucide icon="coins" class="w-4 h-4"/>&nbsp;{{ $order->user->htmlPayment() }}&nbsp;
            @if($edit)
            <button class="btn btn-warning-soft btn-sm ml-1">
                <x-base.lucide icon="pencil" class="w-4 h-4"/>
            </button>
            @endif
        </div>
    </div>
</div>
