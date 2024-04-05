<div class="grid grid-cols-12 gap-x-6">
    <div class="col-span-12">
        <div class="font-medium text-center lg:text-left text-base">Контакты клиенты</div>
        <div class="flex flex-row items-center lg:items-start mt-4">
            <div class="truncate sm:whitespace-normal flex items-center">
                <x-base.lucide icon="user"
                               class="w-4 h-4"/>&nbsp;<a href="{{ route('admin.users.show', $order->user) }}">{{ $order->user->delivery->fullname->getFullName() }}</a>
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4">
                <x-base.lucide icon="mail" class="w-4 h-4"/>&nbsp;<a
                    href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a>
            </div>
            <div class="truncate sm:whitespace-normal flex items-center ml-4">
                <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ $order->user->phone }}
            </div>
        </div>
    </div>

    <div class="col-span-12">
        <div class="flex flex-col items-center lg:items-start mt-2">
            <div class="truncate sm:whitespace-normal flex items-center">
                <x-base.lucide icon="contact"
                               class="w-4 h-4"/>&nbsp;{{ $order->getManager()->fullname->getFullName() }} -
                менеджер
            </div>
        </div>
    </div>
</div>
