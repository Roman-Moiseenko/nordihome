<h2 class=" mt-3 font-medium">Контакты клиенты</h2>

<div class="box p-3 flex flex-row items-center lg:items-start mt-4">
    <div class="truncate sm:whitespace-normal flex items-center my-auto">
        <x-base.lucide icon="user" class="w-4 h-4"/>&nbsp;
        <a href="{{ route('admin.users.show', $refund->order->user) }}">{{ $refund->order->userFullName() }}</a>
    </div>
    <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
        <x-base.lucide icon="mail" class="w-4 h-4"/>&nbsp;<a
            href="mailto:{{ $refund->order->user->email }}">{{ $refund->order->user->email }}</a>
    </div>
    <div class="truncate sm:whitespace-normal flex items-center ml-4 my-auto">
        <x-base.lucide icon="phone" class="w-4 h-4"/>&nbsp;{{ $refund->order->user->phone }}
    </div>
</div>

<h2 class=" mt-3 font-medium">Информация о возврате</h2>
<div class="box p-3 flex flex-col items-center lg:items-start mt-2">
    <div class="truncate sm:whitespace-normal flex items-center">
        <a href="{{ route('admin.sales.order.show', $refund->order) }}" class="flex text-success font-medium">
        <x-base.lucide icon="file"
                       class="w-4 h-4"/>&nbsp;Заказ&nbsp;{{ $refund->order->htmlNum() . ' от ' . $refund->order->htmlDate() }}
        </a>
    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="contact"
                       class="w-4 h-4"/>&nbsp;Ведущий менеджер&nbsp;{{ $refund->order->getManager()->fullname->getFullName() }}

    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="contact"
                       class="w-4 h-4"/>&nbsp;Возврат&nbsp;{{ $refund->staff->fullname->getFullName() }}

    </div>
    <div class="truncate sm:whitespace-normal flex items-center">
        <x-base.lucide icon="message-circle-more"
                       class="w-4 h-4"/>&nbsp;Комментарий&nbsp;<span class="font-medium">{{ $refund->comment }}</span>
    </div>

    <div class="truncate sm:whitespace-normal flex items-center font-medium mt-2">
        Сумма возврата:&nbsp; {{ price($refund->amount) }}
    </div>
</div>


