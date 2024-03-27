<x-base.table.tr>
    <x-base.table.td class="w-20">
        {{ $payment->created_at->format('d-m') }}
    </x-base.table.td>
    <x-base.table.td class="w-40">{{ price($payment->amount) }}</x-base.table.td>
    <x-base.table.td class="text-center"> {{ $payment->nameType() }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ $payment->purposeHTML() }} </x-base.table.td>
    <x-base.table.td class="text-center"> {{ $payment->comment }} </x-base.table.td>
    <x-base.table.td class="text-center">
        {{ is_null($payment->paid_at) ? '-' : $payment->paid_at->format('d-m H:i') }} {!! !empty($payment->document) ? '<br>' . $payment->document : '' !!}
    </x-base.table.td>
    <x-base.table.td class="w-40">
        @if($order->isManager())
            <a class="flex items-center text-danger" href="#"
               data-tw-toggle="modal" data-tw-target="#id-delete-payment"
               data-route= {{ route('admin.sales.order.del-payment', $payment) }}
            >
                <x-base.lucide icon="trash-2" class="w-4 h-4"/>
                Delete </a>
        @endif
        @if($order->isAwaiting() && !$payment->isPaid() && empty($payment->document))
                <x-base.popover class="inline-block mt-auto w-100 mt-2" placement="bottom-start">
                    <x-base.popover.button as="x-base.button" variant="primary" class="w-100">Оплачено
                        <x-base.lucide class="w-4 h-4 ml-2" icon="ChevronDown"/>
                    </x-base.popover.button>
                    <x-base.popover.panel>
                        <form action="{{ route('admin.sales.order.paid-payment', $payment) }}" METHOD="POST">
                            @csrf
                            <div class="p-2">
                                <x-base.form-input name="payment-document" class="flex-1 mt-2" type="text" value=""
                                                   placeholder="Документ"/>

                                <div class="flex items-center mt-3">
                                    <x-base.button id="close-add-group" class="w-32 ml-auto"
                                                   data-tw-dismiss="dropdown" variant="secondary" type="button">
                                        Отмена
                                    </x-base.button>
                                    <x-base.button class="w-32 ml-2" variant="primary" type="submit">
                                        Сохранить
                                    </x-base.button>
                                </div>
                            </div>
                        </form>
                    </x-base.popover.panel>
                </x-base.popover>
        @endif
    </x-base.table.td>
</x-base.table.tr>
