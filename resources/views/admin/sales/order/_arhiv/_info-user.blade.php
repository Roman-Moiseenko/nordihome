<div class="font-medium text-center lg:text-left lg:mt-5 text-lg">Контакты клиенты</div>
<div class="flex flex-col justify-center items-center lg:items-start mt-4">
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="user"
                       class="w-4 h-4"/>&nbsp;{{ $order->userFullName() }}
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="mail" class="w-4 h-4"/>&nbsp;<a
            href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ $order->user->phone }}
    </div>
</div>
